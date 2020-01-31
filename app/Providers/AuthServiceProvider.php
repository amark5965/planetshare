<?php

namespace App\Providers;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use App\User;
use Symfony\Component\HttpFoundation\Response;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(Request $request)
    {
    //  $user_check=User::where('id',$request->user_id)->count();
    //  if($user_check)
    //  {
        $this->registerPolicies();
             Passport::routes();
    //  }
    //  else {
      //  dd('User is not registered with us');
      //}

    }
}
