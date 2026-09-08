<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\LibraryBorrow;
use App\Models\Student;

class LibraryController extends Controller
{
    public function books(Request $request)
    {
        $query = Book::withCount(['borrows as active_borrows_count' => fn($q) => $q->whereNull('returned_at')]);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('author', 'like', "%{$request->search}%")
                  ->orWhere('isbn', 'like', "%{$request->search}%");
            });
        }

        $books = $query->paginate(25);
        $categories = Book::distinct()->pluck('category');

        return view('library.books', compact('books', 'categories'));
    }

    public function storeBook(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'author'        => 'required|string|max:255',
            'isbn'          => 'nullable|string|unique:books,isbn',
            'category'      => 'required|string',
            'total_copies'  => 'required|integer|min:1',
        ]);

        Book::create([
            'title'            => $request->title,
            'author'           => $request->author,
            'isbn'             => $request->isbn,
            'category'         => $request->category,
            'total_copies'     => $request->total_copies,
            'available_copies' => $request->total_copies,
        ]);

        return redirect()->route('library.books')->with('success', 'Book registered successfully.');
    }

    public function borrows(Request $request)
    {
        $query = LibraryBorrow::with(['book', 'student'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $borrows  = $query->paginate(25);
        $books    = Book::where('available_copies', '>', 0)->orderBy('title')->get();
        $students = Student::where('status', 'active')->orderBy('first_name')->get();

        return view('library.borrows', compact('borrows', 'books', 'students'));
    }

    public function issueBorrow(Request $request)
    {
        $request->validate([
            'book_id'    => 'required|exists:books,id',
            'student_id' => 'required|exists:students,id',
            'due_at'     => 'required|date|after:today',
        ]);

        $book = Book::findOrFail($request->book_id);

        if ($book->available_copies < 1) {
            return redirect()->back()->withErrors(['book_id' => 'No copies of this book are currently available.']);
        }

        LibraryBorrow::create([
            'book_id'    => $book->id,
            'student_id' => $request->student_id,
            'borrowed_at'=> now(),
            'due_at'     => $request->due_at,
            'status'     => 'borrowed',
        ]);

        $book->decrement('available_copies');

        return redirect()->route('library.borrows')->with('success', 'Book issued successfully.');
    }

    public function returnBorrow(int $id)
    {
        $borrow = LibraryBorrow::with('book')->findOrFail($id);

        if ($borrow->returned_at) {
            return redirect()->back()->withErrors(['error' => 'This book has already been returned.']);
        }

        $fine = $borrow->calculatedFine;

        $borrow->update([
            'returned_at' => now(),
            'status'      => 'returned',
            'fine_amount' => $fine,
        ]);

        $borrow->book->increment('available_copies');

        return redirect()->route('library.borrows')
            ->with('success', "Book returned" . ($fine > 0 ? " with a fine of MWK " . number_format($fine, 2) . "." : " successfully."));
    }
}
