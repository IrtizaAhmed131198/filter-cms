<div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">

            {{-- Dashboard / Home --}}
            <li class="nav-item">
                <a href="javascript:;"><i class="la la-home"></i><span class="menu-title">Home</span></a>
                <ul class="menu-content">
                    <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a class="menu-item" href="{{ route('admin.dashboard') }}">
                            <i></i> <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a class="menu-item" href="{{ url('') }}">
                            <i></i><span>Visit Website</span>
                        </a>
                    </li>

                    {{-- Permission Management --}}
                    @canAccess('manage_permissions')
                        <li class="{{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                            <a class="menu-item" href="{{ route('admin.permissions.index') }}">
                                <i></i><span>Permission Management</span>
                            </a>
                        </li>
                    @endcanAccess

                    {{-- Favicon Management --}}
                    @canAccess('view_favicon')
                        <li class="{{ request()->routeIs('admin.favicon.edit') ? 'active' : '' }}">
                            <a class="menu-item" href="{{ route('admin.favicon.edit') }}">
                                <i></i><span>Favicon Management</span>
                            </a>
                        </li>
                    @endcanAccess

                    {{-- Logo Management --}}
                    @canAccess('view_logo')
                        <li class="{{ request()->routeIs('admin.logo.edit') ? 'active' : '' }}">
                            <a class="menu-item" href="{{ route('admin.logo.edit') }}">
                                <i></i><span>Logo Management</span>
                            </a>
                        </li>
                    @endcanAccess

                    {{-- User Management --}}
                    @canAccess('view_users')
                        <li class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <a class="menu-item" href="{{ route('admin.users.index') }}">
                                <i></i><span>User Management</span>
                            </a>
                        </li>
                    @endcanAccess

                    {{-- Role Management --}}
                    {{-- @canAccess('manage_roles')
                        <li class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                            <a class="menu-item" href="{{ route('admin.roles.index') }}">
                                <i></i><span>Role Management</span>
                            </a>
                        </li>
                    @endcanAccess --}}

                    {{-- Banner Management --}}
                    <li class="{{ request()->routeIs('admin.banner.*') ? 'active' : '' }}">
                        <a class="menu-item" href="{{ route('admin.banner.index') }}">
                            <i></i><span>Banner Management</span>
                        </a>
                    </li>

                    {{-- Config --}}
                    <li class="{{ request()->routeIs('admin.config.setting') ? 'active' : '' }}">
                        <a class="menu-item" href="{{ route('admin.config.setting') }}">
                            <i></i><span>Config</span>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Inquiries --}}
            <li class="nav-item">
                <a href="javascript:;"><i class="la la-share-alt"></i><span class="menu-title">Inquiries</span></a>
                <ul class="menu-content">
                    <li class="{{ request()->routeIs('admin.contact.inquiries*') ? 'active' : '' }}">
                        <a class="menu-item" href="{{ route('admin.contact.inquiries') }}"><i></i>
                            <span>Contact Inquiries</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.newsletter.inquiries*') ? 'active' : '' }}">
                        <a class="menu-item" href="{{ route('admin.newsletter.inquiries') }}"><i></i>
                            <span>Newsletter Inquiries</span>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- CMS --}}
            <li class="nav-item">
                <a href="javascript:;"><i class="la la-list"></i><span class="menu-title">CMS</span></a>
                <ul class="menu-content">
                    <li class="{{ request()->routeIs('admin.pages*') ? 'active' : '' }}">
                        <a class="menu-item" href="{{ route('admin.pages.index') }}"><i></i>
                            <span>Pages Content</span>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Ecommerce --}}
            <li class="nav-item">
                <a href="javascript:;"><i class="la la-shopping-cart"></i><span class="menu-title">Ecommerce</span></a>
                <ul class="menu-content">
                    <li class="{{ request()->routeIs('admin.attribute..*') ? 'active' : '' }}">
                        <a class="menu-item" href="{{ route('admin.attribute.index') }}"><i></i><span>Attributes</span></a>
                    </li>
                    <li class="{{ request()->routeIs('admin.attributesvalue.*') ? 'active' : '' }}">
                        <a class="menu-item" href="{{ route('admin.attributesvalue.index') }}"><i></i><span>Attribute Values</span></a>
                    </li>
                    <li class="{{ request()->routeIs('admin.category*') ? 'active' : '' }}">
                        <a class="menu-item" href="{{ route('admin.category.index') }}"><i></i><span>Categories</span></a>
                    </li>
                    <li class="{{ request()->routeIs('admin.subcategory*') ? 'active' : '' }}">
                        <a class="menu-item" href="{{ route('admin.subcategory.index') }}"><i></i><span>SubCategories</span></a>
                    </li>
                    <li class="{{ request()->is('admin/product*') ? 'active' : '' }}">
                        <a class="menu-item" href="{{ url('admin/product') }}"><i></i><span>Products</span></a>
                    </li>
                    <li class="{{ request()->is('admin/order/list*') ? 'active' : '' }}">
                        <a class="menu-item" href="{{ url('admin/order/list') }}"><i></i><span>Orders</span></a>
                    </li>
                </ul>
            </li>

            {{-- Testimonials --}}
            <li class="nav-item {{ request()->is('admin/testimonial*') ? 'active' : '' }}">
                <a href="{{ url('admin/testimonial') }}"><i class="la la-quote-left"></i>
                    <span class="menu-title">Testimonials</span>
                </a>
            </li>

            {{-- Account Settings --}}
            <li class="nav-item">
                <a href="{{ url('admin/account/settings') }}"><i class="la la-cog"></i>
                    <span class="menu-title">Account Settings</span>
                </a>
            </li>

        </ul>
    </div>
</div>
