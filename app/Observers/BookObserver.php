<?php

namespace App\Observers;

use App\Models\Author;
use App\Models\Book;

class BookObserver
{
    public function saving(Book $book): void
    {
        if ($book->author_id && !$book->author) {
            $author = Author::find($book->author_id);
            if ($author) {
                $book->author = $author->full_name;
            }
        }
    }

    public function saved(Book $book): void
    {
        if (!$book->author_id) {
            return;
        }

        $book->authors()->syncWithoutDetaching([$book->author_id]);
    }
}
