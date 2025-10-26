
     <!-- Sidebar -->
     <div class="sidebar print:hidden">
         <!-- Main Sidebar -->
         <div class="main-sidebar">
             <div
                 class="flex h-full w-full flex-col items-center border-r border-slate-150 bg-white dark:border-navy-700 dark:bg-navy-800">
                 <!-- Application Logo -->
                 <div class="flex pt-4">
                     <a href="/">
                         <img class="size-11 transition-transform duration-500 ease-in-out hover:rotate-[360deg]"
                             src="{{ asset('images/app-logo.svg') }}" alt="logo" />
                     </a>
                 </div>
                 <!-- Main Sections Links -->
                 <div class="is-scrollbar-hidden flex grow flex-col space-y-4 overflow-y-auto pt-6">
                     <!-- Dashobards -->
                     <a href="{{ route('dashboard') }}"
                        class="@if ($sectionMenu === 'main-menu')
                        flex size-11 items-center justify-center rounded-lg bg-primary/10 text-primary outline-hidden transition-colors duration-200 hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25 dark:bg-navy-600 dark:text-accent-light dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90
                        @else
                        flex size-11 items-center justify-center rounded-lg outline-hidden transition-colors duration-200 hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25
                        @endif"
                         x-tooltip.placement.right="'Main'">
                         <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                             <path
                                 d="M9.85714 3H4.14286C3.51167 3 3 3.51167 3 4.14286V9.85714C3 10.4883 3.51167 11 4.14286 11H9.85714C10.4883 11 11 10.4883 11 9.85714V4.14286C11 3.51167 10.4883 3 9.85714 3Z"
                                 fill="currentColor" />
                             <path
                                 d="M9.85714 12.8999H4.14286C3.51167 12.8999 3 13.4116 3 14.0428V19.757C3 20.3882 3.51167 20.8999 4.14286 20.8999H9.85714C10.4883 20.8999 11 20.3882 11 19.757V14.0428C11 13.4116 10.4883 12.8999 9.85714 12.8999Z"
                                 fill="currentColor" fill-opacity="0.3" />
                             <path
                                 d="M19.757 3H14.0428C13.4116 3 12.8999 3.51167 12.8999 4.14286V9.85714C12.8999 10.4883 13.4116 11 14.0428 11H19.757C20.3882 11 20.8999 10.4883 20.8999 9.85714V4.14286C20.8999 3.51167 20.3882 3 19.757 3Z"
                                 fill="currentColor" fill-opacity="0.3" />
                             <path
                                 d="M19.757 12.8999H14.0428C13.4116 12.8999 12.8999 13.4116 12.8999 14.0428V19.757C12.8999 20.3882 13.4116 20.8999 14.0428 20.8999H19.757C20.3882 20.8999 20.8999 20.3882 20.8999 19.757V14.0428C20.8999 13.4116 20.3882 12.8999 19.757 12.8999Z"
                                 fill="currentColor" fill-opacity="0.3" />
                         </svg>
                     </a>


                     <!-- Forms -->
                     <a href="{{ route('addons') }}"
                        class="@if ($sectionMenu === 'secondary-menu')
                        flex size-11 items-center justify-center rounded-lg bg-primary/10 text-primary outline-hidden transition-colors duration-200 hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25 dark:bg-navy-600 dark:text-accent-light dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90
                        @else
                        flex size-11 items-center justify-center rounded-lg outline-hidden transition-colors duration-200 hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25
                        @endif"
                        x-tooltip.placement.right="'Addons'">
                         <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                             <path fill-opacity="0.25"
                                 d="M21.0001 16.05V18.75C21.0001 20.1 20.1001 21 18.7501 21H6.6001C6.9691 21 7.3471 20.946 7.6981 20.829C7.7971 20.793 7.89609 20.757 7.99509 20.712C8.31009 20.586 8.61611 20.406 8.88611 20.172C8.96711 20.109 9.05711 20.028 9.13811 19.947L9.17409 19.911L15.2941 13.8H18.7501C20.1001 13.8 21.0001 14.7 21.0001 16.05Z"
                                 fill="currentColor" />
                             <path fill-opacity="0.5"
                                 d="M17.7324 11.361L15.2934 13.8L9.17334 19.9111C9.80333 19.2631 10.1993 18.372 10.1993 17.4V8.70601L12.6384 6.26701C13.5924 5.31301 14.8704 5.31301 15.8244 6.26701L17.7324 8.17501C18.6864 9.12901 18.6864 10.407 17.7324 11.361Z"
                                 fill="currentColor" />
                             <path
                                 d="M7.95 3H5.25C3.9 3 3 3.9 3 5.25V17.4C3 17.643 3.02699 17.886 3.07199 18.12C3.09899 18.237 3.12599 18.354 3.16199 18.471C3.20699 18.606 3.252 18.741 3.306 18.867C3.315 18.876 3.31501 18.885 3.31501 18.885C3.32401 18.885 3.32401 18.885 3.31501 18.894C3.44101 19.146 3.585 19.389 3.756 19.614C3.855 19.731 3.95401 19.839 4.05301 19.947C4.15201 20.055 4.26 20.145 4.377 20.235L4.38601 20.244C4.61101 20.415 4.854 20.559 5.106 20.685C5.115 20.676 5.11501 20.676 5.11501 20.685C5.25001 20.748 5.385 20.793 5.529 20.838C5.646 20.874 5.76301 20.901 5.88001 20.928C6.11401 20.973 6.357 21 6.6 21C6.969 21 7.347 20.946 7.698 20.829C7.797 20.793 7.89599 20.757 7.99499 20.712C8.30999 20.586 8.61601 20.406 8.88601 20.172C8.96701 20.109 9.05701 20.028 9.13801 19.947L9.17399 19.911C9.80399 19.263 10.2 18.372 10.2 17.4V5.25C10.2 3.9 9.3 3 7.95 3ZM6.6 18.75C5.853 18.75 5.25 18.147 5.25 17.4C5.25 16.653 5.853 16.05 6.6 16.05C7.347 16.05 7.95 16.653 7.95 17.4C7.95 18.147 7.347 18.75 6.6 18.75Z"
                                 fill="currentColor" />
                         </svg>
                     </a>

                 </div>
                 <!-- Bottom Links -->
                 @include('components.sidebar-profile')

             </div>
         </div>
         <!-- Sidebar Panel -->
         <div class="sidebar-panel">
             <div class="flex h-full grow flex-col bg-white pl-[var(--main-sidebar-width)] dark:bg-navy-750">
                 <!-- Sidebar Panel Header -->
                 <div class="flex h-18 w-full items-center justify-between pl-4 pr-1">
                     <p class="text-base tracking-wider text-slate-800 dark:text-navy-100"> {{ $titleMenus ?? '' }} </p>
                     <button @click="$store.global.isSidebarExpanded = false"
                         class="btn h-7 w-7 rounded-full p-0 text-primary hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:text-accent-light/80 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25 xl:hidden">
                         <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                 d="M15 19l-7-7 7-7" />
                         </svg>
                     </button>
                 </div>

                <!-- Sidebar Panel Body -->
                @if ($sectionMenu === 'main-menu')
                    @include('components.sidebar.main-menu')
                @elseif ($sectionMenu === 'secondary-menu')
                    @include('components.sidebar.secondary-menu')
                @endif



             </div>
         </div>
     </div>