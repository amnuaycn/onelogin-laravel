## Onelogin
### Step 1. Create Module Onelogin > https://github.com/SocialiteProviders/Generators

php artisan make:socialite Onelogin --spec=oauth2 --author=Amnuay --email=ch.amnuay@gmail.com

### Step 2. Edit code  SocialiteProviders/src/Onelogin/Provider.php  
Add function from document https://developers.onelogin.com/openid-connect

### Step 3. Copy .env.example to .env
Edit  Add 
```
ONELOGIN_CLIENT_ID=<YOUR_CLIENT_ID>
ONELOGIN_CLIENT_SECRET=<YOUR_CLIENT_SECRET>
ONELOGIN_REDIRECT_URI=http://localhost:8000/auth/onelogin/callback
ONELOGIN_BASE_URL=https://<SUBDOMAIN>.onelogin.com/oidc/2
```

### Step 4. Replace database/migrations/2014_10_12_000000_create_users_table.php with:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->text('token');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
```

### Step 5. Edit app/Models/User.php with:
```php

protected $fillable = [
        'name',
        'email',
        'token',
    ];
```
### Step 6. php artisan migrate

### Step 7. Add to the $providers array in config/app.php to configure the Socialite provider:
```php
$providers = [
    ...
    \SocialiteProviders\Manager\ServiceProvider::class,
    ...
]
```
### Step 8. Add the following to the $listen array in app/Providers/EventServiceProvider.php:
```php
protected $listen = [
       ...
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            'SocialiteProviders\\Onelogin\\OneloginExtendSocialite@handle',
        ],
    ];
```

### Step 9. update config/services.php:
```php
 'onelogin' => [
        'client_id' => env('ONELOGIN_CLIENT_ID'),
        'client_secret' => env('ONELOGIN_CLIENT_SECRET'),
        'redirect' => env('ONELOGIN_REDIRECT_URI'),
        'base_url' => env('ONELOGIN_BASE_URL')
    ],
```

### Step 10. Generate and Configure Controller
php artisan make:controller OneloginController
```php
public function oneloginRedirect()
    {
        return Socialite::driver('onelogin')->redirect();
    }

    public function callbackOnelogin()
    {
        try {
    
            $user = Socialite::driver('onelogin')->user();
            $isUser = User::where('email', $user->email)->first();
     
            if($isUser){
                Auth::login($isUser);
                return redirect('/dashboard');
            }else{
                $createUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'token' => $user->token
                ]);
    
                Auth::login($createUser);
                return redirect('/dashboard');
            }
    
        } catch (Exception $exception) {
            dd($exception->getMessage());
        }
    }
```


### Test  > php artisan serve


<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
