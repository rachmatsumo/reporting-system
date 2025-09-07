<?php

use Spatie\Menu\Laravel\Menu;
use Spatie\Menu\Laravel\Html;
use App\Services\MenuService;
use Illuminate\Support\Facades\Auth;

// Helper function untuk mengecek apakah menu aktif
if (!function_exists('isMenuActive')) {
    function isMenuActive($menuRoute, $menuUrl = null)
    {
        $currentRouteName = request()->route() ? request()->route()->getName() : '';
        $currentUrl = url()->current();
        $currentPath = request()->path();

        if ($menuRoute && $currentRouteName) {
            if ($currentRouteName === $menuRoute) return true;

            // cek base route resource: user.*, post.*, dll
            $menuBase = explode('.', $menuRoute)[0];
            $currentBase = explode('.', $currentRouteName)[0];
            if ($menuBase && $menuBase === $currentBase) return true;
        }

        if ($menuUrl && $menuUrl !== '#') {
            $menuPath = trim(parse_url($menuUrl, PHP_URL_PATH), '/');
            if ($currentUrl === $menuUrl) return true;
            if ($menuPath && str_starts_with($currentPath, $menuPath)) return true;
        }

        return false;
    }
}

// ULTRA STRICT: Cek apakah user bisa akses menu ini
if (!function_exists('userCanAccessMenu')) {
    function userCanAccessMenu($menu, $user)
    {
        // Jika ada permission, user harus punya permission tersebut
        if (!empty($menu->permission)) {
            return $user->can($menu->permission);
        }
        
        // Jika tidak ada permission, return false (ultra strict)
        // Ubah ke true jika menu tanpa permission dianggap bisa diakses semua
        return false;
    }
}

if (!function_exists('sideBarMenu')) {
    function sideBarMenu()
    {
        $menuService = app(MenuService::class);
        $menus = $menuService->getMenu();
        $user = Auth::user();

        if (!$user) return '';

        $menu = Menu::new()->addClass('navbar-nav');

        foreach ($menus[null] ?? [] as $item) {

            // Parent menu dengan submenu (Level 1)
            if (isset($menus[$item->id])) {
                
                // BUILD SUBMENU FIRST, baru tentukan apakah parent ditampilkan
                $submenuHtml = '';
                $isSubActive = false;

                foreach ($menus[$item->id] as $child) {
                    $childUrl = $child->route ? route($child->route) : ($child->url ?? '#');
                    $childIcon = formatMenuIcon($child->icon ?? '');

                    // Check kalau ada sub-child (Level 3)
                    if (isset($menus[$child->id])) {
                        $subChildHtml = '';
                        $isSubChildActive = false;

                        // Build accessible sub-children first
                        foreach ($menus[$child->id] as $subChild) {
                            // Skip sub-child yang tidak punya akses
                            if (!userCanAccessMenu($subChild, $user)) {
                                continue;
                            }

                            $subChildUrl = $subChild->route ? route($subChild->route) : ($subChild->url ?? '#');
                            $subChildIcon = formatMenuIcon($subChild->icon ?? '');

                            $subChildActiveClass = isMenuActive($subChild->route, $subChildUrl) ? 'active' : '';
                            if ($subChildActiveClass) {
                                $isSubChildActive = true;
                                $isSubActive = true;
                            }

                            $subChildHtml .= '<li><a class="nav-link fs-6 ps-4 ' . $subChildActiveClass . '" href="' . $subChildUrl . '">' . $subChildIcon . '<span class="ms-1">' . $subChild->title . '</span></a></li>';
                        }

                        // Level 2 HANYA ditampilkan jika:
                        // 1. User punya akses ke Level 2 ini, ATAU
                        // 2. Ada minimal 1 Level 3 yang bisa diakses
                        $canAccessLevel2Directly = userCanAccessMenu($child, $user);
                        $hasAccessibleSubChildren = !empty($subChildHtml);
                        
                        if ($canAccessLevel2Directly || $hasAccessibleSubChildren) {
                            $collapseShowSubChild = $isSubChildActive ? 'show' : '';
                            $childActiveClass = ($isSubChildActive || isMenuActive($child->route, $childUrl)) ? 'active' : '';

                            $submenuHtml .= '
                                <li>
                                    <a class="nav-link fs-6 ' . $childActiveClass . '" data-bs-toggle="collapse" href="#collapse-' . $child->id . '" role="button" aria-expanded="' . ($isSubChildActive ? 'true' : 'false') . '" aria-controls="collapse-' . $child->id . '">
                                        ' . $childIcon . '<span class="ms-1">' . $child->title . '</span>
                                    </a>
                                    <div class="navbar-nav collapse ms-3 mt-2 ' . $collapseShowSubChild . '" id="collapse-' . $child->id . '">
                                        <ul class="nav flex-column fs-6">
                                            ' . $subChildHtml . '
                                        </ul>
                                    </div>
                                </li>';
                        }

                    } else {
                        // Level 2 tanpa submenu - HARUS punya akses langsung
                        if (!userCanAccessMenu($child, $user)) {
                            continue; // SKIP
                        }

                        $childActiveClass = isMenuActive($child->route, $childUrl) ? 'active' : '';
                        if ($childActiveClass) $isSubActive = true;

                        $submenuHtml .= '<li><a class="nav-link fs-6 ' . $childActiveClass . '" href="' . $childUrl . '">' . $childIcon . '<span class="ms-1">' . $child->title . '</span></a></li>';
                    }
                }

                // PARENT HANYA DITAMPILKAN jika ada submenu yang bisa ditampilkan
                if (!empty($submenuHtml)) {
                    $icon = formatMenuIcon($item->icon ?? '');
                    $collapseShow = $isSubActive ? 'show' : '';

                    $menu->add(
                        Html::raw(
                            '<li class="nav-item">
                                <a class="nav-link fs-6 ' . ($isSubActive ? 'active' : '') . '" data-bs-toggle="collapse" href="#collapse-' . $item->id . '" role="button" aria-expanded="' . ($isSubActive ? 'true' : 'false') . '" aria-controls="collapse-' . $item->id . '">
                                    <div class="icon icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                        ' . $icon . '
                                    </div>
                                    <span class="nav-link-text ms-1 mb-0">' . $item->title . '</span>
                                </a>
                                <div class="navbar-nav collapse ms-3 mt-2 ' . $collapseShow . '" id="collapse-' . $item->id . '">
                                    <ul class="nav flex-column fs-6"> 
                                        ' . $submenuHtml . '
                                    </ul>
                                </div>
                            </li>'
                        )
                    );
                }

            } else {
                // Single menu (Level 1 tanpa submenu)
                if (!userCanAccessMenu($item, $user)) {
                    continue; // SKIP
                }

                $url = $item->route ? route($item->route) : ($item->url ?? '#');
                $icon = formatMenuIcon($item->icon ?? '');
                $activeClass = isMenuActive($item->route, $url) ? 'active' : '';

                $menu->add(
                    Html::raw(
                        '<li class="nav-item">
                            <a class="nav-link fs-6 ' . $activeClass . '" href="' . $url . '">
                                <div class="icon icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    ' . $icon . '
                                </div>
                                <span class="nav-link-text ms-1 mb-0">' . $item->title . '</span>
                            </a>
                        </li>'
                    )
                );
            }
        }

        return $menu->render();
    }
}

// Helper function untuk icon
if (!function_exists('formatMenuIcon')) {
    function formatMenuIcon($iconName)
    {
        if (empty($iconName)) return '';
        if (str_contains($iconName, '<i ') || str_contains($iconName, '<svg')) return $iconName . ' ';

        // Auto-detect icon library
        if (str_starts_with($iconName, 'fa-') || str_starts_with($iconName, 'fas ') || str_starts_with($iconName, 'far ')) {
            if (!str_contains($iconName, 'fa ')) $iconName = 'fa ' . $iconName;
        } elseif (str_starts_with($iconName, 'bi-') || str_starts_with($iconName, 'bi bi-')) {
            if (!str_starts_with($iconName, 'bi bi-')) $iconName = str_replace('bi-', 'bi bi-', $iconName);
        } else {
            $iconName = 'bi bi-' . $iconName;
        }

        return '<i style="font-size: 1rem;" class="ps-2 pe-2 text-center '.$iconName.'"></i> ';
    }
}