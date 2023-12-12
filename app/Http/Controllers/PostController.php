<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Http\Requests\PostRequest; // useする

class PostController extends Controller
{
    public function index(Post $post)
    {
        return view('posts.index')->with(['posts' => $post->getPaginateByLimit()]);
        //Guzzleのクライアントオブジェクトを作成する
        $client = new \GuzzleHttp\Client();

        // GET通信するURL
        $url = 'https://teratail.com/api/v1/questions';

        //$clientでGETメソッドを使用して、指定された$urlからデータを取得
        $response = $client->request(
            'GET',
            $url,
            //リクエストに追加するヘッダー情報
            //設定されたAPIトークンにアクセスするため
            ['Bearer' => config('services.teratail.token')]
        );
            // API通信で取得したデータはjson形式なので
            // PHPファイルに対応した連想配列にデコードする
            $questions = json_decode($response->getBody(), true);
            // index bladeに取得したデータを渡す
            return view('posts.index')->with([
                'posts' => $post->getPaginateByLimit(),
                'questions' => $questions['questions'],
            ]);
    }

    public function show(Post $post)
    {
        return view('posts.show')->with(['post' => $post]);
    }

    public function create(Category $category)
    {
        return view('posts.create')->with(['categories'=>$category->get()]);
    }
    
    public function store(Post $post, PostRequest $request) // 引数をRequestからPostRequestにする
    {
        $input = $request['post'];
        $post->fill($input)->save();
        return redirect('/posts/' . $post->id);
    }
    
    public function delete(Post $post)
    {
        $post->delete();
        return redirect('/');
    }
}
?>