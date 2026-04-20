@extends(template().'layouts.app')
@section('content')

    <!-- shop -->
    <section class="shop">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="filter-body">
                        <div class="filter-body-top">
                            <div class="sorting-container">
                                <button class="btn-1" type="button" data-bs-toggle="offcanvas"
                                        data-bs-target="#offcanvasShop" aria-controls="offcanvasShop">
                                    <i class="fa-solid fa-bars-sort"></i> @lang('Filter Product')
                                </button>

                                <div class="sorting-buttton">
                                    <h6>@lang('Showing') <span
                                            id="showProducts">9</span> @lang('of') {{$total_products}} @lang('products')
                                    </h6>
                                </div>
                                <div class="sorting d-flex gap-1 align-items-center" id="Sorting">
                                    <h6>@lang('Sort by'):</h6>
                                    <select class="selectpicker nice-select" name="make">
                                        <option value="asc">@lang('Alphabetically, A-Z')</option>
                                        <option value="desc">@lang('Alphabetically, Z-A')</option>
                                        <option value="low_to_high">@lang('Price, low to high')</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="product-wrapper">
                            <div class="row g-4" id="allProduct">

                            </div>
                            <div class="paigination">
                                <ul id="paigination">

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- shop -->

    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasShop" aria-labelledby="offcanvasShopLabel">
        <div class="offcanvas-header">
            <div class="logo">
                <a href="{{route('page')}}"><img src="{{logo(true)}}" alt="logo"></a>
            </div>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="shop-left-aside">
                <div class="shop-filter">
                    <h6>@lang('Filters')</h6>
                    <div class="clear-all">
                        <a href="javascript:void(0);" class="clearButton">@lang('Clear All')</a>
                    </div>
                </div>
                <div class="accordion" id="accordionPanelsStayOpenExample">
                    <div class="accordion-item clear-btn-group">
                        <h5 class="accordion-header" id="panelsStayOpen-headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true"
                                    aria-controls="panelsStayOpen-collapseOne">
                                @lang('Categories')
                            </button>
                        </h5>
                        <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show">
                            <div class="accordion-body">
                                <div class="filter-form">
                                    @foreach($categories as $category)
                                        <label class="custom-checkbox category-checkbox"
                                               data-id="{{$category->id}}">
                                            <input type="checkbox" class="checkbox">
                                            <span class="checkmark"></span>
                                            <span class="label-text">@lang($category->name)</span>
                                            <span
                                                class="stock-number float-end">({{count($category->products??[])}})</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item clear-btn">
                        <h5 class="accordion-header" id="panelsStayOpen-headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false"
                                    aria-controls="panelsStayOpen-collapseTwo">
                                @lang('Material')
                            </button>
                        </h5>
                        <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse show">
                            <div class="accordion-body">
                                <div class="filter-form">
                                    @foreach($materials as $material)
                                        <label class="custom-checkbox material-checkbox"
                                               data-id="{{$material->id}}">
                                            <input type="checkbox" class="checkbox">
                                            <span class="checkmark"></span>
                                            <span class="label-text">@lang($material->material)</span>
                                            <span
                                                class="stock-number float-end">({{count($material->products??[])}})</span>
                                        </label>
                                    @endforeach

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item clear-btn">
                        <h5 class="accordion-header" id="panelsStayOpen-headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false"
                                    aria-controls="panelsStayOpen-collapseThree">
                                @lang('Karat')
                            </button>
                        </h5>
                        <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse show">
                            <div class="accordion-body">
                                <div class="filter-form">
                                    @foreach($karats as $karat)
                                        <label class="custom-checkbox karat-checkbox" data-id="{{$karat->id}}">
                                            <input type="checkbox" class="checkbox">
                                            <span class="checkmark"></span>
                                            <span class="label-text">{{$karat->karat}}K</span>
                                            <span
                                                class="stock-number float-end">({{count($karat->products??[])}})</span>
                                        </label>
                                    @endforeach

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sidebar-box">
                    <div class="sidebar-tag pb_20">
                        <h5>@lang('By Price')</h5>
                    </div>
                    <div class="sidebar-range-slider">
                        <div id="priceRange"></div>
                        <div class="slider-labels">
                            <span id="minLabel">{{currencyPosition($min_price)}}</span>
                            <span id="maxLabel">{{currencyPosition($max_price)}}</span>
                        </div>
                        <p>@lang('Price'): <span id="minDisplay">{{currencyPosition($min_price)}}</span> - <span
                                id="maxDisplay">{{currencyPosition($max_price)}}</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let lastCallTime = 0;
            const debounceDelay = 100;
            let categories = [];
            let materials = [];
            let karats = [];
            let minPrice = Number('{{$min_price}}');
            let maxPrice = Number('{{$max_price}}');
            let sorting = null;

            async function getProducts(url = null, changePosition = false) {
                const currentTime = Date.now();
                if (currentTime - lastCallTime < debounceDelay) {
                    return;
                }
                lastCallTime = currentTime;

                await axios.get(url ?? '{{route('products')}}', {
                    params: {
                        category: categories ?? [],
                        material: materials ?? [],
                        karat: karats ?? [],
                        min_price: minPrice,
                        max_price: maxPrice,
                        sorting: sorting
                    }
                })
                    .then((response) => {
                        if (changePosition === true) {
                            window.scrollTo({
                                top: 300,
                                left: 0,
                                behavior: 'smooth'
                            });
                        }
                        document.getElementById('showProducts').textContent = response.data.data.length;
                        generateProductHtml(response.data.data)
                        generatePaginationHtml(response.data)
                        Notiflix.Loading.remove();
                    })
                    .catch((error) => {
                        Notiflix.Loading.remove();
                        console.error(error)
                    })
            }

            function isNumeric(value) {
                return /^[+-]?\d+(\.\d+)?$/.test(value);
            }

            getProducts();

            function generateProductHtml(products) {
                let html = '';
                if (products.length > 0) {
                    let user = '{{auth()->user()??false}}'
                    let icon = '';
                    let loginRoute = '{{route('login')}}'
                    products.map((product) => {
                        let route = "{{ route('product.details', [':slug']) }}";
                        if (user != false) {
                            var UserId = '{{auth()->guard('web')->user()->id??null}}';
                            icon = `<li><a href="javascript:void(0)" data-id="${product.id}" class="${wishlist(product.wishlist, UserId) ? 'addedWishlist' : ''} addToWishlist"><i class="fa-light fa-heart"></i></a></li>`
                        } else {
                            icon = `<li><a href="${loginRoute}"><i class="fa-light fa-heart"></i></a></li>`
                        }
                        route = route.replace(':slug', product.slug ?? 'slug');
                        html += `<div class="col-xl-3 col-lg-4 col-md-6">
                                    <div class="shop-single wow fadeInUp" data-wow-delay="200ms" data-wow-duration="1000ms">
                                        <div class="shop-image-container">
                                            <div class="shop-image">
                                                <a href="${route}"><img src="${product.image}" alt="product"></a>
                                            </div>
                                            <div class="shop-icon-list">
                                                <ul>
                                                    ${icon}
                                                    <li><a href="${route}" class="quick-view-btn"><i class="fa-light fa-eye"></i></a></li>

                                                </ul>
                                            </div>
                                        </div>
                                        <div class="product-single-content">
                                            <a href="${route}">${product.details?.title.length > 30 ? product.details?.title.substring(0, 30) + '...' : product.details?.title}</a>
                                            <p>${product.category?.name}</p>
                                            <div class="product-single-button">
                                                <h5>${currencyPosition(Number(product.price) + 0)}</h5>
                                                <button  data-id="${product.id}" data-quantity="1" class="btn-1 addToCart btn-with-loader"><span class="button-text">` + '{{trans('Add to cart')}}' + `</span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>`
                    });

                } else {
                    html = `
                 <div class="row d-flex justify-content-center mt-5">
                                    <div class="col-4 mt-5">
                                        <img src="{{asset($themeTrue.'images/nodata.jpg')}}"  alt="" srcset="">
                                    </div>
                                </div>
                `
                }

                document.getElementById('allProduct').innerHTML = html

                Array.from(document.getElementsByClassName('addToCart')).forEach((item) => {
                    const route = '{{route('addToCart')}}';

                    item.addEventListener('click', async () => {
                        item.classList.add('loading');
                        item.disabled = true;
                        const originalText = item.querySelector('.button-text').innerText;
                        item.querySelector('.button-text').innerText = 'Adding...';

                        const id = item.dataset.id;
                        const quantity = item.dataset.quantity;

                        try {
                            await addToCart(id, quantity, route);
                            item.querySelector('.button-text').innerText = 'Added!';
                        } catch (error) {
                            console.error('Error adding to cart:', error);
                            item.querySelector('.button-text').innerText = 'Error';
                        } finally {
                            setTimeout(() => {
                                item.classList.remove('loading');
                                item.disabled = false;
                                item.querySelector('.button-text').innerText = 'Add To Bag';
                                $('#cart-offcanvas').addClass('popup-visible');
                            }, 1000);

                        }
                    });
                });

            }

            function generatePaginationHtml(data) {
                let links = data.links;
                let html = '';
                if (data.last_page > 1) {
                    links.map((link, index) => {
                        if (index === 0) {
                            html += `<li><a href="javascript:void(0);" data-url="${link.url ?? null}" class="page-number" ><i class="fa-regular fa-angle-left"></i></a></li>`
                        } else if (isNumeric(link.label) && index !== 0) {
                            html += ` <li><a href="javascript:void(0);" class="${data.current_page === Number(link.label) ? 'active' : ''} page-number" data-url="${data.current_page !== Number(link.label) ? link.url ?? null : null}">${link.label}</a></li>`
                        } else {
                            html += ` <li><a href="javascript:void(0);" data-url="${link.url ?? null}" class="page-number"><i class="fa-regular fa-angle-right"></i></a></li>`
                        }

                    })
                } else {
                    $('.paigination').hide()
                }
                document.getElementById('paigination').innerHTML = html;


                Array.from(document.getElementsByClassName('page-number')).forEach((item) => {
                    item.addEventListener('click', function () {
                        Notiflix.Loading.standard('Loading...', {
                            svgColor: '#CE9C00FF',
                            messageColor: '#FFFFFF'
                        });
                        let url = item.dataset.url
                        if (url) {
                            getProducts(url, true)
                        }
                    })
                })
            }

            function handler(event) {
                Notiflix.Loading.standard('Loading...', {
                    svgColor: '#CE9C00FF',
                    messageColor: '#FFFFFF'
                });

                event.stopPropagation();

                let label = event.target.closest('.category-checkbox');
                if (!label) return;

                let id = label.dataset.id;
                const input = label.querySelector('input[type="checkbox"]');

                if (input) {
                    input.checked = !input.checked;

                    if (input.checked) {
                        if (!categories.includes(id)) {
                            categories.push(id);
                        }
                    } else {
                        const index = categories.indexOf(id);
                        if (index !== -1) {
                            categories.splice(index, 1);
                        }
                    }

                    getProducts(null, false);
                }
            }

            function materialHandler() {
                Notiflix.Loading.standard('Loading...', {
                    svgColor: '#CE9C00FF',
                    messageColor: '#FFFFFF'
                });
                event.stopPropagation();


                let label = event.target.closest('.material-checkbox');
                if (!label) return;

                let id = label.dataset.id;
                const input = label.querySelector('input[type="checkbox"]');

                if (input) {
                    input.checked = !input.checked;

                    if (input.checked) {
                        if (!materials.includes(id)) {
                            materials.push(id);
                        }
                    } else {
                        const index = materials.indexOf(id);
                        if (index !== -1) {
                            materials.splice(index, 1);
                        }
                    }

                    getProducts(null, false);
                }
            }

            function karatHandler() {
                Notiflix.Loading.standard('Loading...', {
                    svgColor: '#CE9C00FF',
                    messageColor: '#FFFFFF'
                });
                event.stopPropagation();


                let label = event.target.closest('.karat-checkbox');
                if (!label) return;

                let id = label.dataset.id;
                const input = label.querySelector('input[type="checkbox"]');

                if (input) {
                    input.checked = !input.checked;

                    if (input.checked) {
                        if (!karats.includes(id)) {
                            karats.push(id);
                        }
                    } else {
                        const index = karats.indexOf(id);
                        if (index !== -1) {
                            karats.splice(index, 1);
                        }
                    }

                    getProducts(null, true);
                }
            }

            Array.from(document.getElementsByClassName('category-checkbox')).forEach((item) => {
                item.addEventListener('click', handler, false);
            });


            Array.from(document.getElementsByClassName('material-checkbox')).forEach((item) => {
                item.addEventListener('click', materialHandler, false)
            })


            Array.from(document.getElementsByClassName('karat-checkbox')).forEach((item) => {
                item.addEventListener('click', karatHandler, false)
            })


            // NOUISLIDER (PRICE RANGE)
            if ($('.sidebar-range-slider').length) {
                var priceSlider = document.getElementById('priceRange');

                noUiSlider.create(priceSlider, {
                    start: [Number('{{$min_price}}'), Number('{{$max_price}}')],  // Initial values for both handles
                    connect: true,      // Connects the slider handles with a colored range
                    range: {
                        'min': Number('{{$min_price}}') + 0,      // Minimum value
                        'max': Number('{{$max_price}}') + 0   // Maximum value
                    },
                    format: {
                        to: function (value) {
                            return Math.round(value);
                        },
                        from: function (value) {
                            return Number(value);
                        }
                    }
                });

                // Update the labels dynamically
                priceSlider.noUiSlider.on('change', function (values, handle) {
                    Notiflix.Loading.standard('Loading...', {
                        svgColor: '#CE9C00FF',
                        messageColor: '#FFFFFF'
                    });
                    minPrice = Number(values[0]);
                    maxPrice = Number(values[1]);

                    console.log(minPrice, maxPrice)

                    getProducts(null, false)
                    $('#minDisplay').text(currencyPosition(values[0]));
                    $('#maxDisplay').text(currencyPosition(values[1]));
                    $('#minLabel').text(currencyPosition(values[0]));
                    $('#maxLabel').text(currencyPosition(values[1]));
                });
            }
            // NOUISLIDER (PRICE RANGE)


            $(document).on('change', '#Sorting', function () {
                Notiflix.Loading.standard('Loading...', {
                    svgColor: '#CE9C00FF',
                    messageColor: '#FFFFFF'
                });
                sorting = $('.selectpicker').val();
                getProducts(null, false)
            })

            $(document).on('click', '.clearButton', function () {
                Notiflix.Loading.standard('Loading...', {
                    svgColor: '#CE9C00FF',
                    messageColor: '#FFFFFF'
                });
                categories = [];
                materials = [];
                karats = [];
                minPrice = Number('{{$min_price}}');
                maxPrice = Number('{{$max_price}}');
                sorting = null;
                $('.checkbox').prop('checked', false);
                priceSlider.noUiSlider.set([minPrice, maxPrice]);
                getProducts(null, false);
            })

            function wishlist(menuWishlist, userId) {
                let wishlist = menuWishlist !== undefined || null ? menuWishlist : [];
                for (var i = 0; i < wishlist.length ?? 0; i++) {
                    var wishlistItem = menuWishlist[i];
                    if (wishlistItem.user_id == userId) {
                        return true;
                    }
                }
                return false;
            }
        })
    </script>
@endpush
