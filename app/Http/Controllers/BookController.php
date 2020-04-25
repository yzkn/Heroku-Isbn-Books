<?php
// Copyright (c) 2019 YA-androidapp(https://github.com/YA-androidapp) All rights reserved.

namespace App\Http\Controllers;

use App\Book;
use App\User;
use Auth;
use Request;

class BookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $books = Book::orderBy('summary__isbn', 'asc')->get();
        return view('book.index', ['books' => $books]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $book = null;
        if (strlen(Request::input('isbn')) != 0) {
            $isbn = Request::input('isbn');
        } else if (strlen(Request::input('summary__isbn')) != 0) {
            $isbn = Request::input('summary__isbn');
        } else if (strlen(Request::input('id')) != 0 && \is_numeric(Request::input('id'))) {
            // Duplicate
            $book = Book::find(Request::input('id'));
            if (isset($book)) {
                $isbn = $book->summary_isbn;
            } else {
                $isbn = '';
            }
        } else {
            $isbn = '';
        }

        $r = User::orderBy('name', 'asc')->get();
        $readers = array();
        foreach ($r as $v) {
            $readers[$v->id] = $v->name;
        }

        return view('book.create', ['isbn' => $isbn, 'readers' => $readers, 'book' => $book]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $user = Auth::user();

        $isbn = $request->summary__isbn;
        $isbn = str_replace('-', '', $isbn);
        $isbn = str_replace(' ', '', $isbn);
        if (preg_match("/^([0-9]{10})|([0-9]{13})$/", $isbn)) {
            $book = new Book;
            $book->summary__isbn = preg_replace('/^[ 　]+|[ 　]+$/u', '', $isbn);
            $book->onix__RecordReference = preg_replace('/^[ 　]+|[ 　]+$/u', '', $isbn);
            $book->onix__ProductIdentifier__IDValue = preg_replace('/^[ 　]+|[ 　]+$/u', '', $isbn);
            $book->summary__cover = preg_replace('/^[ 　]+|[ 　]+$/u', '', $request->summary__cover);
            $book->summary__title = preg_replace('/^[ 　]+|[ 　]+$/u', '', $request->summary__title);
            $book->onix__DescriptiveDetail__TitleDetail__TitleText = preg_replace('/^[ 　]+|[ 　]+$/u', '', $request->summary__title);
            $book->summary__author = preg_replace('/^[ 　]+|[ 　]+$/u', '', $request->summary__author);
            $book->summary__publisher = preg_replace('/^[ 　]+|[ 　]+$/u', '', $request->summary__publisher);
            $book->summary__pubdate = preg_replace('/^[ 　]+|[ 　]+$/u', '', $request->summary__pubdate);
            $book->summary__series = preg_replace('/^[ 　]+|[ 　]+$/u', '', $request->summary__series);
            $book->summary__volume =  mb_convert_kana($request->summary__volume, 'n');
            $book->userid = $user->id;
            $book->save();
            return redirect('books/' . $book->id);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Book $book)
    {
        return view('book.show', ['book' => $book]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Auth::user();

        $book = Book::find($id);
        if (isset($book)) {
            $r = User::orderBy('name', 'asc')->get();
            $readers = array();
            foreach ($r as $v) {
                $readers[$v->id] = $v->name;
            }

            return view('book.edit', ['book' => $book, 'user' => $user, 'readers' => $readers, 'isowner' => (($user->id === $book->userid)?true:false)]);
        }
        return redirect('/books');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(\Illuminate\Http\Request $request, $id)
    {
        $user = Auth::user();

        $book = Book::find($id);
        if (isset($book)) {
            if($user->id === $book->userid || ($user->role > 0 && $user->role <= \Config::get('role.admin'))){
                $book->fill($request->all());
            }
            $book->reader_id = $user->id;
            $book->save();
            return redirect('books/' . $book->id);
        }
        return redirect('/books');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user();

        $book = Book::find($id);
        if (isset($book)) {
            $book->delete();
        }
        return redirect('/books');
    }

    public function bd($isbn)
    {
        $url = 'https://api.openbd.jp/v1/get?isbn=' . $isbn;
        if (isOnline($url)) {
            $json = file_get_contents($url);
            if ('' !== $json) {
                $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
                return json_decode($json);
            }
        }
        return '';
    }

    public function ndl($isbn)
    {
        $url = 'http://iss.ndl.go.jp/api/sru?operation=searchRetrieve&query=isbn=' . $isbn;
        if (isOnline($url)) {
            $xml = file_get_contents($url);
            if ('' !== $xml) {
                $xml = mb_convert_encoding($xml, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
                $xml = html_entity_decode($xml, ENT_QUOTES);
                $json = xml2json($xml);
                return $json;
            }
        }
        return '';
    }

    public function index_series()
    {
        $books = Book::orderBy('summary__isbn', 'asc')->get();

        $books_groupby_series = [];
        foreach ($books as $key => $book) {
            $summary__series = $book->summary__series != '' ? $book->summary__series : $book->summary__title;
            $summary__volume = $book->summary__volume != '' ? mb_convert_kana($book->summary__volume, 'as') : 1;

            if (!isset($books_groupby_series[$summary__series][$summary__volume])) {
                $books_groupby_series[$summary__series][$summary__volume] = $book;
            } else {
                $books_groupby_series[$summary__series][mt_rand(100, 999) + intval($summary__volume)] = $book;
            }
        }

        // Sort
        $max_cols = 0;
        ksort($books_groupby_series);
        foreach ($books_groupby_series as $key => $books_in_series) {
            ksort($books_groupby_series[$key]);

            if (count($books_in_series) > $max_cols) {
                $max_cols = count($books_in_series);
            }
        }

        return view(
            'book.series',
            [
                'books_groupby_series' => $books_groupby_series,
                'colspan' => $max_cols,
            ]
        );
    }

    public function search_series(\Illuminate\Http\Request $request)
    {
        $query = Book::query();
        foreach ($request->only(['summary__series']) as $key => $val) {
            $query->where($key, 'like', '%'.$val.'%');
        }
        $books = $query->orderBy('summary__isbn', 'asc')->get();

        $books_groupby_series = [];
        foreach ($books as $key => $book) {
            $summary__series = $book->summary__series != '' ? $book->summary__series : $book->summary__title;
            $summary__volume = $book->summary__volume != '' ? mb_convert_kana($book->summary__volume, 'as') : 1;

            if (!isset($books_groupby_series[$summary__series][$summary__volume])) {
                $books_groupby_series[$summary__series][$summary__volume] = $book;
            } else {
                $books_groupby_series[$summary__series][mt_rand(100, 999) + intval($summary__volume)] = $book;
            }
        }

        // Sort
        $max_cols = 0;
        ksort($books_groupby_series);
        foreach ($books_groupby_series as $key => $books_in_series) {
            ksort($books_groupby_series[$key]);

            if (count($books_in_series) > $max_cols) {
                $max_cols = count($books_in_series);
            }
        }

        return view(
            'book.series',
            [
                'books_groupby_series' => $books_groupby_series,
                'colspan' => $max_cols,
            ]
        );
    }
}

function isOnline($uri)
{
    if ((null === $uri) || ('' === $uri) || (strpos($uri, 'http') === 0)) {
        $uri = 'https://openbd.jp/';
    }

    $isOnline = false;
    try {
        $isOnline = fsockopen(parse_url($uri, PHP_URL_HOST), 80, $errno, $errstr, 10);
    } catch (\ErrorException $eex) {
    } catch (\Exception $ex) {
    }
    return $isOnline;
}

function xml2json($xml)
{
    // 名前空間が記述されているxmlファイルをそのまま読み込むと、当該タグのデータが欠落する
    $xml = preg_replace("/<([^>]+?):([^>]+?)>/", "<$1_$2>", $xml);
    $xml = preg_replace("/_\/\//", "://", $xml);
    $objXml = simplexml_load_string($xml, null, LIBXML_NOCDATA);
    xml2jsonsub($objXml);
    $json = json_encode($objXml, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return preg_replace('/\\\\\//', '/', $json);
}

function xml2jsonsub($node)
{
    if ($node->count() > 0) {
        foreach ($node->children() as $child) {
            foreach ($child->attributes() as $key => $val) {
                $node->addChild($child->getName() . "@" . $key, $val);
            }
            xml2jsonsub($child);
        }
    }
}
