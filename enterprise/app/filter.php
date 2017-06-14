<?php




Route::filter('auth', function()
{
     if (Auth::guest())
     {
          if (Request::ajax())
          {
               return Response::make('Unauthorized', 401);
          }
          else
          {
               return Redirect::guest('/');
          }
     }
});
 