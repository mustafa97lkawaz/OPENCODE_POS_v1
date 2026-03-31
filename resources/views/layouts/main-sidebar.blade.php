<!-- main-sidebar -->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar sidebar-scroll">
    <div class="main-sidebar-header active">
        <a class="desktop-logo logo-light active" href="{{ url('/' . ($page = 'index')) }}"><img
                src="{{ URL::asset('assets/img/brand/logo.png') }}" class="main-logo" alt="logo"></a>
        <a class="desktop-logo logo-dark active" href="{{ url('/' . ($page = 'index')) }}"><img
                src="{{ URL::asset('assets/img/brand/logo-white.png') }}" class="main-logo dark-theme" alt="logo"></a>
        <a class="logo-icon mobile-logo icon-light active" href="{{ url('/' . ($page = 'index')) }}"><img
                src="{{ URL::asset('assets/img/brand/favicon.png') }}" class="logo-icon" alt="logo"></a>
        <a class="logo-icon mobile-logo icon-dark active" href="{{ url('/' . ($page = 'index')) }}"><img
                src="{{ URL::asset('assets/img/brand/favicon-white.png') }}" class="logo-icon dark-theme"
                alt="logo"></a>
    </div>
    <div class="main-sidemenu">
        <div class="app-sidebar__user clearfix">
            <div class="dropdown user-pro-body">
                <div class="">
                    <img alt="user-img" class="avatar avatar-xl brround"
                        src="{{ URL::asset('assets/img/faces/6.jpg') }}"><span
                        class="avatar-status profile-status bg-green"></span>
                </div>
                <div class="user-info">
                    <h4 class="font-weight-semibold mt-3 mb-0">{{ Auth::user()->name }}</h4>
                    <span class="mb-0 text-muted">{{ Auth::user()->email }}</span>
                </div>
            </div>
        </div>
        <ul class="side-menu">
            <li class="side-item side-item-category">الرئيسية</li>
            <li class="slide">
                <a class="side-menu__item" href="{{ url('/' . ($page = 'home')) }}"><svg
                        xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0V0z" fill="none" />
                        <path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3" />
                        <path
                            d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z" />
                    </svg><span class="side-menu__label">الرئيسية</span></a>
            </li>

            <!-- نقاط البيع -->
            <li class="side-item side-item-category">نقاط البيع (POS)</li>
            <li class="slide">
                <a class="side-menu__item" href="{{ route('pos') }}"><svg
                        xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0V0z" fill="none" />
                        <path d="M17 2H7c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 18H7V4h10v16z" opacity=".3" />
                        <path d="M17 2H7c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 18H7V4h10v16zM8 6h3v2H8zm0 4h3v2H8zm0 4h3v2H8zm4-8h5v2h-5zm0 4h5v2h-5zm0 4h5v2h-5z" />
                    </svg><span class="side-menu__label">شاشة البيع (POS)</span></a>
            </li>
            <li class="slide">
                <a class="side-menu__item" href="{{ url('/sales') }}"><svg
                        xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0V0z" fill="none" />
                        <path d="M19 5H5v14h14V5zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z" opacity=".3" />
                        <path d="M3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2zm2 0h14v14H5V5zm2 5h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z" />
                    </svg><span class="side-menu__label">المبيعات</span></a>
            </li>
            <li class="slide">
                <a class="side-menu__item" href="{{ route('suspended.index') }}"><svg
                        xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0V0z" fill="none" />
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" opacity=".3" />
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                    </svg><span class="side-menu__label">المبيعات المعلقة</span></a>
            </li>

            <!-- المخزون -->
            <li class="side-item side-item-category">المخزون</li>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('#') }}"><svg
                        xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0V0z" fill="none" />
                        <path d="M20 2H4c-1 0-2 .9-2 2v3.01c0 .72.43 1.34 1 1.69V20c0 1.1 1.1 2 2 2h14c.9 0 2-.9 2-2V8.7c.57-.35 1-.97 1-1.69V4c0-1.1-1-2-2-2zm-5 12H9v-2h6v2zm5-7H4V4h16v3z" opacity=".3" />
                        <path d="M20 2H4c-1 0-2 .9-2 2v3.01c0 .72.43 1.34 1 1.69V20c0 1.1 1.1 2 2 2h14c.9 0 2-.9 2-2V8.7c.57-.35 1-.97 1-1.69V4c0-1.1-1-2-2-2zm-5 12H9v-2h6v2zm5-7H4V4h16v3z" />
                    </svg><span class="side-menu__label">المخزون</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('/products') }}">المنتجات</a></li>
                    <li><a class="slide-item" href="{{ url('/categories') }}">التصنيفات</a></li>
                    <li><a class="slide-item" href="{{ url('/stock_adjustments') }}">تعديلات المخزون</a></li>
                </ul>
            </li>

            <!-- العملاء والموردين -->
            <li class="side-item side-item-category">العملاء والموردين</li>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('#') }}"><svg
                        xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0V0z" fill="none" />
                        <path d="M15 11V4H4v8.17l.59-.58.58-.59H6z" opacity=".3" />
                        <path d="M21 6h-2v9H6v2c0 .55.45 1 1 1h11l4 4V7c0-.55-.45-1-1-1zm-5 7c.55 0 1-.45 1-1V3c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v14l4-4h10zM4.59 11.59l-.59.58V4h11v7H5.17l-.58.59z" />
                    </svg><span class="side-menu__label">العملاء والموردين</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('/customers') }}">العملاء</a></li>
                    <li><a class="slide-item" href="{{ url('/suppliers') }}">الموردين</a></li>
                </ul>
            </li>

            <!-- التقارير -->
            <li class="side-item side-item-category">التقارير</li>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('#') }}"><svg
                        xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0V0z" fill="none" />
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z" opacity=".3" />
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zM7 10h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z" />
                    </svg><span class="side-menu__label">التقارير</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('reports.dashboard') }}">لوحة التحكم</a></li>
                    <li><a class="slide-item" href="{{ route('reports.sales') }}">تقرير المبيعات</a></li>
                    <li><a class="slide-item" href="{{ route('reports.inventory') }}">تقرير المخزون</a></li>
                    <li><a class="slide-item" href="{{ route('reports.profit') }}">تقرير الارباح</a></li>
                </ul>
            </li>

            <!-- المصروفات -->
            <li class="side-item side-item-category">المصروفات</li>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('#') }}"><svg
                        xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0V0z" fill="none" />
                        <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.56-3.5 3.55 0 2.84 2.35 3.55 3.86 3.55 1.72 0 2.88-.87 2.88-2.18 0-1.01-.77-1.86-2.27-2.46l-1.17-.56c-.87-.42-1.52-.87-1.52-1.75 0-1.01 1.02-1.75 2.54-1.75 1.54 0 2.52.74 2.54 1.75h2.21c-.06-1.5-1.12-2.75-3.75-2.71V3h3v2.15c1.86.19 3.27 1.51 3.27 3.52 0 2.59-2.24 3.47-3.86 3.47z" opacity=".3" />
                        <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.56-3.5 3.55 0 2.84 2.35 3.55 3.86 3.55 1.72 0 2.88-.87 2.88-2.18 0-1.01-.77-1.86-2.27-2.46l-1.17-.56c-.87-.42-1.52-.87-1.52-1.75 0-1.01 1.02-1.75 2.54-1.75 1.54 0 2.52.74 2.54 1.75h2.21c-.06-1.5-1.12-2.75-3.75-2.71V3h3v2.15c1.86.19 3.27 1.51 3.27 3.52 0 2.59-2.24 3.47-3.86 3.47z" />
                    </svg><span class="side-menu__label">المصروفات</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('/expense_categories') }}">تصنيفات المصروفات</a></li>
                    <li><a class="slide-item" href="{{ url('/expenses') }}">المصروفات</a></li>
                </ul>
            </li>

            <!-- الاعدادات -->
            <li class="side-item side-item-category">الاعدادات</li>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('#') }}"><svg
                        xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" class="side-menu__icon"
                        viewBox="0 0 24 24">
                        <g>
                            <rect fill="none" />
                        </g>
                        <g>
                            <g />
                            <g>
                                <path d="M21,5c-1.11-0.35-2.33-0.5-3.5-0.5c-1.95,0-4.05,0.4-5.5,1.5c-1.45-1.1-3.55-1.5-5.5-1.5S2.45,4.9,1,6v14.65 c0,0.25,0.25,0.5,0.5,0.5c0.1,0,0.15-0.05,0.25-0.05C3.1,20.45,5.05,20,6.5,20c1.95,0,4.05,0.4,5.5,1.5c1.35-0.85,3.8-1.5,5.5-1.5 c1.65,0,3.35,0.3,4.75,1.05c0.1,0.05,0.15,0.05,0.25,0.05c0.25,0,0.5-0.25,0.5-0.5V6C22.4,5.55,21.75,5.25,21,5z M3,18.5V7 c1.1-0.35,2.3-0.5,3.5-0.5c1.34,0,3.13,0.41,4.5,0.99v11.5C9.63,18.41,7.84,18,6.5,18C5.3,18,4.1,18.15,3,18.5z M21,18.5 c-1.1-0.35-2.3-0.5-3.5-0.5c-1.34,0-3.13,0.41-4.5,0.99V7.49c1.37-0.59,3.16-0.99,4.5-0.99c1.2,0,2.4,0.15,3.5,0.5V18.5z" />
                            </g>
                        </g>
                    </svg><span class="side-menu__label">الاعدادات</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('/settings') }}">اعدادات النظام</a></li>
                    @can('قائمة المستخدمين')
                        <li><a class="slide-item" href="{{ url('/users') }}">المستخدمين</a></li>
                    @endcan
                    @can('صلاحيات المستخدمين')
                        <li><a class="slide-item" href="{{ url('/roles') }}">الصلاحيات</a></li>
                    @endcan
                </ul>
            </li>
        </ul>
    </div>
</aside>
<!-- main-sidebar -->
