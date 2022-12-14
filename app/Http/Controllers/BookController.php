<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $book = Book::orderBy('judul','DESC')->get();
        $response = [
            'message'=>'List nama  book order by judul',
            'data' =>$book
        ];
        return response()->json($response,Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {        
        $nama = mt_rand(1, 9999);
        $today = Carbon::today()->toDateString();
        $date = str_replace('-','',$today);        
        $ext_foto = $request->file('foto')->getClientOriginalExtension();
        $foto_file = $date."-".$nama.".". $ext_foto;         
        $path = $request->file('foto')->storeAs('image',$foto_file);
           
        $validator = Validator::make($request->all(),[
            'judul' =>['required'],
            'genre' =>['required'],
            'author' =>['required'],
            'terbit' =>['required'],
            'foto' =>['required'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(),Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {        
            $book =Book::create([
                'judul' => request('judul'),
                'genre' => request('genre'),
                'author' => request('author'),
                'terbit' => request('terbit'),
                'foto' => $path                
            ]);

            $response = [
                'message' => 'Book Create',
                'data' => $book
            ];
            return response()->json($response, Response::HTTP_CREATED);
        } catch (QueryException $e) {
            return response()->json([
                'message'=> "FAILED ". $e->errorInfo
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $book = Book::findOrFail($id);
        $response = [
            'message' => 'DETAIL',
            'data' => $book
        ];
        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, Book $book)
    {
        
        

        $validasi= $request->validate([
            'judul'=>'required',
            'genre'=>'required',
            'author'=>'required',
            'terbit'=>'required',
            'foto'=>''
        ]);

        try {
            $response = Book::find($id);        
            if ($request->file('foto')){

                $old =  $response->foto;
                File::delete('image',$old);

                $nama = mt_rand(1, 9999);
                $today = Carbon::today()->toDateString();
                $date = str_replace('-','',$today);        
                $ext_foto = $request->file('foto')->getClientOriginalExtension();
                $foto_file = $date."-".$nama.".". $ext_foto;         
                $path = $request->file('foto')->storeAs('image',$foto_file);
                $validasi['foto']=$path;                
            }
            
            $response->update($validasi);
            return response()->json([
                "success" =>true,
                "message"=>'success'
            ]);
        } catch (\Exception $e){
            return response()->json([
                "message" =>'Err',
                "errors"=>$e->getMessage()
            ]);
        }
    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $book = Book::findOrFail($id);            
        try {

            $old =  $book->foto;
            File::delete('image',$old);

            $book->delete();

            $response = [
                'message' => 'Book Delete'
            ];
            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'message'=> "FAILED ". $e->errorInfo
            ]);
        }
    }
}
