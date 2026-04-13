<?php

return [
    App\Providers\AppServiceProvider::class,
    Cooperl\Database\DB2\DB2ServiceProvider::class,
    App\Providers\DB2CompatServiceProvider::class,  // parche cooperl + Laravel 13
];