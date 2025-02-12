<?php

namespace Modules\Shop\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Shop\Entities\Address;
use Modules\Shop\Entities\Cart;
use Modules\shop\Repositories\front\interfaces\ProductRepositoryInterfaces;
use Modules\shop\Repositories\front\ProductRepository;

use Modules\shop\Repositories\front\interfaces\CategoryRepositoryInterfaces;
use Modules\Shop\Repositories\front\CategoryRepository;

use Modules\shop\Repositories\front\interfaces\TagRepositoryInterfaces;
use Modules\Shop\Repositories\front\TagRepository;

use Modules\Shop\Repositories\front\interfaces\CartRepositoryInterfaces;
use Modules\Shop\Repositories\front\CartRepository;

use Modules\Shop\Repositories\front\AddressRepository;
use Modules\Shop\Repositories\front\interfaces\AddressRepositoryInterfaces;



use Modules\Shop\Entities\Category;
use Modules\Shop\Repositories\front\interfaces\OrderRepositoryInterface;
use Modules\Shop\Repositories\front\OrderRepository;

class ShopServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Shop';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'shop';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        $this ->registerRepositories();

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
    private function registerRepositories()
    {
        $this->app->bind(
        ProductRepositoryInterfaces::class,
        ProductRepository::class
        );

        $this->app->bind(
            CategoryRepositoryInterfaces::class,
            CategoryRepository::class
            );

        $this->app->bind(
            TagRepositoryInterfaces::class,
            TagRepository::class
        );

        $this->app->bind(
            CartRepositoryInterfaces::class,
            CartRepository::class
        );

        $this->app->bind(
            AddressRepositoryInterfaces::class,
            AddressRepository::class
        );
        
        $this->app->bind(
            OrderRepositoryInterface::class,
            OrderRepository::class
        );


    }
}
