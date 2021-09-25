@extends('layouts.app')

@section('content')
    <div class="row">
        <div id="left-sidebar" style="padding: 0;background: #f0f0f0;">
            <div class="logo-section">
                <a href="{{\Illuminate\Support\Facades\URL::to('/')}}"><img src="{{asset('uploads/title_white.png')}}" class="main-logo" alt="logo"></a>
                <i class="fas fa-bars burger-mobi" onclick="showHideSidebar();"></i>
            </div>
            <ul id="left-menu">
                <li class="li-parent"><a href="/start"><i class="fas fa-angle-double-right"></i>&nbsp;Старт</a></li>
                <li class="sub-menu li-parent">
                    <a href="#">
                        <i class="fas fa-balance-scale"></i>&nbsp;Оценка
                        <div class='fa fa-caret-down right'></div>
                    </a>
                    <ul>
                        <li><a href="/profitability">Доходность заказов</a></li>
                    </ul>
                </li>
                @if (auth('admin')->check() || auth('organization')->check())
                    <li class="li-parent"><a href="/contracts"><i class="fas fa-shopping-cart"></i>&nbsp;Заказы</a></li>
                @endif
                <li class="li-parent"><a href="/trips"><i class="fas fa-car"></i>&nbsp;Рейсы</a></li>
                <li class="sub-menu li-parent">
                    <a href="#">
                        <i class="fas fa-chalkboard-teacher"></i>&nbsp;Действия
                        <div class='fa fa-caret-down right'></div>
                    </a>
                    <ul>
                        <li><a href="/incomes">Доходы</a></li>
                        <li><a href="/expenses">Расходы</a></li>
                        <li><a href="/refuels">Заправки</a></li>
                    </ul>
                </li>
                @if (auth('admin')->check() || auth('organization')->check())
                    <li class="sub-menu li-parent">
                        <a href="#">
                            <i class="fas fa-chart-area"></i>&nbsp;Аналитика
                            <div class='fa fa-caret-down right'></div>
                        </a>
                        <ul>
                            <li><a href="/transactions">Баланс водителей</a></li>
                            <li><a href="/customers">Расчёты с заказчиками</a></li>
                        </ul>
                    </li>
                    <li class="sub-menu li-parent">
                        <a href="#">
                            <i class="fas fa-cogs"></i>&nbsp;Настройки
                            <div class='fa fa-caret-down right'></div>
                        </a>
                        <ul>
                            <li><a href="/cars">Автомобили</a></li>
                            <li><a href="/drivers">Водители</a></li>
                            <li><a href="/counterparties">Контрагенты</a></li>
                            <?php if(auth('admin')->check()): ?>
                                <li><a href="/organizations">Организации</a></li>
                            <?php endif; ?>
                            <?php if(auth('organization')->check()): ?>
                                <li><a href="/organizations/{{auth('organization')->id()}}/edit">Организация</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <li class="sub-menu li-parent">
                        <a href="#">
                            <i class="fas fa-folder-open"></i>&nbsp;Справочники
                            <div class='fa fa-caret-down right'></div>
                        </a>
                        <ul>
                            <li><a href="/expense_categories">Категории расходов</a></li>
                            <li><a href="/departure_points">Пункты отправления</a></li>
                            <li><a href="/destinations">Пункты назначения</a></li>
                            <li><a href="/intermediate_points">Пункты следования</a></li>
                            <li><a href="/stops_and_services">Стоянки и сервисы</a></li>
                            <li><a href="/payment_types">Форма оплаты</a></li>
                            <li><a href="/cargo_types">Типы грузов</a></li>
                            <li><a href="/unit_types">Единицы измерения</a></li>
                        </ul>
                    </li>
                @endif

                @if (auth('admin')->check())
                    <li class="li-parent"><a href="/yoo_payments"><i class="fas fa-credit-card"></i>&nbsp;История платежей</a></li>
                    <li class="li-parent"><a href="/car_payment_history"><i class="fas fa-truck"></i>&nbsp;История оплаты автомобилей</a></li>
                @endif
            </ul>
        </div>
        <div class="crud-content">
            <div class="content-header">
                <i class="fas fa-bars burger-sidebar" onclick="toggleSidebar();"></i>
                <h4 class="ml-3 d-inline">Ускорение и автоматизация транспортных компаний</h4>
                <div style="width:100px; float:right;">
                    <ul class="navbar-nav">
                        @if(!auth('admin')->check() && session()->has('admin_id'))
                            <li class="nav-item">
                                <a class="dropdown-item-custom" href="{{ route('auth.return_to_admin') }}">Вернуться к админу</a>
                            </li>
                        @endif
                        @if(auth()->user())
                        <li class="nav-item">
                            <a class="dropdown-item-custom" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                 document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                        @endif
                    </ul>

                </div>
            </div>

            <div class="mt-3 mb-3 pl-3">
                @if (Request::has('payment_id'))
                    <div class="box">
                        <div class="box-body">
                            <div class="col-sm-12">
                                @include('payments.payment_status', ['payment_id' => Request::get('payment_id')])
                            </div>
                        </div>
                    </div>
                @endif

                @yield('crud-content')
            </div>
        </div>
    </div>
@endsection
