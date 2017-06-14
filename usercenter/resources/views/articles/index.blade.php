<h1>这是index.blade.php</h1>

 @foreach($articles as $article)

 		<h1><a href="{{ url('articles',$article->id) }}">{{ $article->title }}</a></h1>
 		<!-- <h1><a href="{{ action('ArticleController@show',[$article->id]) }}">{{ $article->title }}</a></h1> -->
        <!-- <h1><a href="/articles/{{ $article->id }}">{{ $article->title }}</a></h1> -->
      	<p>{{ $article->intro }}</p>
        <hr>
 @endforeach