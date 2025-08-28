<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <a href="#" class="nav-link">
                <div class="nav-profile-image">
                    <img src="{{ get_setting('favicon')['value'] }}" alt="profile">
                    <span class="login-status online"></span>
                </div>
                <div class="nav-profile-text d-flex flex-column">
                    <span class="font-weight-bold mb-2">{{ auth()->user()->name }}</span>
                    <span class="text-secondary text-small">{{ auth()->user()->role }}</span>
                </div>
                <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
            </a>
        </li>
        <li class="nav-item {{ $activePage == 'dashboard' ? 'active' : null }}">
            <a class="nav-link" href="{{ route('home') }}">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
            </a>
        </li>
        @canany(['View Product Category', 'View Product Item', 'View Product Item Category'])
        <li class="nav-item {{ in_array($activePage, config('array.menu.product')) ? 'active' : null }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#general-pages" aria-expanded="false" aria-controls="general-pages">
                <span class="menu-title">Product</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-briefcase menu-icon"></i>
            </a>
            <div class="collapse {{ in_array($activePage, config('array.menu.product')) ? 'show' : null }}" id="general-pages">
                <ul class="nav flex-column sub-menu">
                    @can ('View Product Category')
                      <li class="nav-item">
                        <a class="nav-link {{ $activePage == 'product_category' ? 'active' : null }}" href="{{ route('product_category.index') }}"> Category </a>
                      </li>
                    @endcan
                    @can ('View Product')
                    <li class="nav-item">
                        <a class="nav-link {{ $activePage == 'product' ? 'active' : null }}" href="{{ route('product.index') }}"> Product </a>
                    </li>
                    @endcan
                    @can ('View Product Item')
                    <li class="nav-item">
                        <a class="nav-link {{ in_array($activePage, ['product_item']) ? 'active' : null }}" href="{{ route('product_item.index') }}"> Item </a>
                    </li>
                    @endcan
                    @can ('View Product Item Category')
                      <li class="nav-item">
                        <a class="nav-link {{ in_array($activePage, ['product_item_category']) ? 'active' : null }}" href="{{ route('product_item_category.index') }}">Item Category </a>
                      </li>
                    @endcan
                </ul>
            </div>
        </li>
        @endcan
        @can ('View Order')
        <li class="nav-item {{ $activePage == 'order' ? 'active' : null }}">
            <a class="nav-link" href="{{ route('order.index') }}">
                <span class="menu-title">Order</span>
                <i class="mdi mdi-basket menu-icon"></i>
            </a>
        </li>
        @endcan
        @canany(['View Discount', 'View Slider'])
        <li class="nav-item {{ in_array($activePage, config('array.menu.promo')) ? 'active' : null }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#promo-pages" aria-expanded="false" aria-controls="promo-pages">
                <span class="menu-title">Promo</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-bell menu-icon"></i>
            </a>
            <div class="collapse {{ in_array($activePage, config('array.menu.promo')) ? 'show' : null }}" id="promo-pages">
                <ul class="nav flex-column sub-menu">
                    @can ('View Discount')
                    <li class="nav-item">
                        <a class="nav-link {{ $activePage == 'discount' ? 'active' : null }}" href="{{ route('discount.index') }}"> Discount </a>
                    </li>
                    @endcan
                    @can ('View Slider')
                    <li class="nav-item">
                        <a class="nav-link {{ $activePage == 'slider' ? 'active' : null }}" href="{{ route('slider.index') }}"> Slider </a>
                    </li>
                    @endcan
                    @can ('View Flash Sales')
                    <li class="nav-item">
                        <a class="nav-link {{ $activePage == 'flash_sale' ? 'active' : null }}" href="{{ route('flash_sale.index') }}">Flash Sales</a>
                    </li>
                    @endcan
                </ul>
            </div>
        </li>
        @endcan
        <li class="nav-item {{ $activePage == 'deposit' ? 'active' : null }}">
            <a class="nav-link" href="{{ route('deposit.index') }}">
                <span class="menu-title">Deposit</span>
                <i class="mdi mdi-cash menu-icon"></i>
            </a>
        </li>
        @can ('View Report')
        <li class="nav-item {{ $activePage == 'report' ? 'active' : null }}">
            <a class="nav-link" href="{{ route('report.index') }}">
                <span class="menu-title">Report</span>
                <i class="mdi mdi-book menu-icon"></i>
            </a>
        </li>
        @endcan
        @can('View Statistic')
        <li class="nav-item {{ in_array($activePage, config('array.menu.statistic')) ? 'active' : null }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#statistic-pages" aria-expanded="false" aria-controls="statistic-pages">
                <span class="menu-title">Statistic</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-chart-areaspline menu-icon"></i>
            </a>
            <div class="collapse {{ in_array($activePage, config('array.menu.statistic')) ? 'show' : null }}" id="statistic-pages">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ $activePage == 'statistic_order' ? 'active' : null }}" href="{{ route('statistic.order') }}"> Statistic Order </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activePage == 'statistic_user' ? 'active' : null }}" href="{{ route('statistic.user') }}"> Statistic User </a>
                    </li>
                </ul>
            </div>
        </li>
        @endcan
        @canany(['View User', 'View Role'])
        <li class="nav-item {{ in_array($activePage, config('array.menu.user')) ? 'active' : null }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#user-pages" aria-expanded="false" aria-controls="user-pages">
                <span class="menu-title">User</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-account menu-icon"></i>
            </a>
            <div class="collapse {{ in_array($activePage, config('array.menu.user')) ? 'show' : null }}" id="user-pages">
                <ul class="nav flex-column sub-menu">
                     @can ('View User')
                     <li class="nav-item">
                         <a class="nav-link {{ $activePage == 'user' ? 'active' : null }}" href="{{ route('user.index') }}"> Non-Customer </a>
                     </li>
                     @endcan
                    @can ('View Customer')
                    <li class="nav-item">
                        <a class="nav-link {{ $activePage == 'customer' ? 'active' : null }}" href="{{ route('user.customer') }}"> Customer </a>
                    </li>
                    @endcan
                    @can ('View Permission')
                    <li class="nav-item">
                        <a class="nav-link {{ $activePage == 'role' ? 'active' : null }}" href="{{ route('role.index') }}"> Permissions </a>
                    </li>
                    @endcan
                </ul>
            </div>
        </li>
        @endcan
        @can ('View Payment Methods')
        <li class="nav-item {{ $activePage == 'payment_method' ? 'active' : null }}">
            <a class="nav-link" href="{{ route('payment_method.index') }}">
                <span class="menu-title">Payment Method</span>
                <i class="mdi mdi-currency-usd menu-icon"></i>
            </a>
        </li>
        @endcan
        @can ('View Blog')
          <li class="nav-item {{ $activePage == 'blog' ? 'active' : null }}">
            <a class="nav-link" href="{{ route('blog.index') }}">
              <span class="menu-title">Blog</span>
              <i class="mdi mdi-newspaper menu-icon"></i>
            </a>
          </li>
        @endcan
        @can ('View Setting')
          <li class="nav-item {{ $activePage == 'setting' ? 'active' : null }}">
            <a class="nav-link" href="{{ route('setting.index') }}">
              <span class="menu-title">Setting</span>
              <i class="mdi mdi-cogs menu-icon"></i>
            </a>
          </li>
        @endcan
    </ul>
</nav>
