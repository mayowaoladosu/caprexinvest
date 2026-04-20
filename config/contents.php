<?php
return [
    'banner_section' => [
        'single' => [
            'field_name' => [
                'title' => 'text',
                'description' => 'text',
                'button' => 'text',
                'button_url' => 'url',
                'heading' => 'text',
                'number_of_ratings' => 'number',
                'powered_by' => 'file',
                'image' => 'file',
                'client_image_1' => 'file',
                'client_image_2' => 'file',
                'client_image_3' => 'file',
            ],
            'validation' => [
                'title.*' => 'required|max:100',
                'description.*' => 'required|max:500',
                'button.*' => 'required|max:40',
                'button_url.*' => 'nullable',
                'heading.*' => 'required|max:50',
                'powered_by.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
                'image.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
                'client_image_1.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
                'client_image_2.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
                'client_image_3.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
                'number_of_ratings.*' => 'nullable|numeric',

            ],
            'notice' => [
                'powered_by' => 'Please upload an image with dimensions of 118*30 pixels for the best quality.',
                'image' => 'Please upload an image with dimensions of 1105*1380 pixels for the best quality.',
                'client_image_3' => 'Upload a 50x51 pixel image for optimal client image quality.',
            ]
        ],
        'multiple' => [
            'field_name' => [
                'brand' => 'file',
            ],
            'validation' => [
                'brand.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
            ],
            'notice' => [
                'brand' => 'Please upload an image with dimensions of 231*66 pixels for the best quality.',
            ]
        ],
        'preview' => [
            'Banner Section One' =>  'assets/themes/light/images/sections/banner_one.png',
        ]

    ],
    'banner_section_two' => [
        'single' => [
            'field_name' => [
                'title' => 'text',
                'short_description' => 'text',
                'button' => 'text',
                'button_url' => 'url',
                'video_title' => 'text',
                'video_url' => 'url',
                'review_title' => 'text',
                'review_text' => 'text',
                'image' => 'file'
            ],
            'validation' => [
                'title.*' => 'required|max:100',
                'description.*' => 'required|max:500',
                'button.*' => 'required|max:40',
                'button_url.*' => 'nullable',
                'video_title.*' => 'nullable|max:50',
                'video_url.*' => 'nullable',
                'review_title.*' => 'nullable|max:50',
                'review_text.*' => 'nullable|max:100',
                'image.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
            ],
            'notice' => [
                'image' => 'Upload a 584x576 pixel image for optimal image quality.',
            ]
        ],
        'multiple' => [
            'field_name' => [
                'reviewer' => 'file',
            ],
            'validation' => [
                'reviewer.*' => 'required|max:3072|image|mimes:jpg,jpeg,png',
            ],
            'notice' => [
                'reviewer' => 'Upload a 50x51 pixel image for optimal image quality.',
            ]
        ],
        'preview' => [
            'Banner Section Two' => 'assets/themes/light/images/sections/banner_two.png',
        ]
    ],
    'about_section' => [
        'single' => [
            'field_name' => [
                'title' => 'text',
                'sub_title' => 'text',
                'description' => 'text',
                'about_image_1' => 'file',
                'about_image_2' => 'file',
            ],
            'validation' => [
                'title.*' => 'required|max:50',
                'sub_title.*' => 'required|max:100',
                'description.*' => 'required|max:500',
                'about_image_1.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
                'about_image_2.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png'
            ],
            'notice' => [
                'about_image_1' => 'Upload a 1271x1310 pixel image for optimal image quality.',
                'about_image_2' => 'Upload a 366x168 pixel image for optimal image quality.'
            ]
        ],
        'multiple' => [
            'field_name' => [
                'icon' => 'text',
                'title' => 'text',
                'short_description' => 'text',
            ],
            'validation' => [
                'icon.*' => 'required',
                'title.*' => 'required|max:60',
                'short_description.*' => 'required|max:250',
            ]
        ],
        'preview' => [
            'About' => 'assets/themes/light/images/sections/about.png',
        ]
    ],
    'about_section_two' => [
        'single' => [
            'field_name' => [
                'title' => 'text',
                'sub_title' => 'text',
                'description' => 'text',
                'button' => 'text',
                'button_url' => 'url',
                'banner_icon' => 'file',
                'banner_title_1' => 'text',
                'banner_sub_title_1' => 'text',
                'banner_title_2' => 'text',
                'banner_sub_title_2' => 'text',
                "banner_image" => 'file'
            ],
            'validation' => [
                'title.*' => 'required|max:50',
                'sub_title.*' => 'required|max:100',
                'description.*' => 'required|max:500',
                'button.*' => 'required|max:40',
                'button_url.*' => 'nullable',
                'banner_icon.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
                'banner_title_1.*' => 'required|max:50',
                'banner_sub_title_1.*' => 'required|max:50',
                'banner_title_2.*' => 'required|max:50',
                'banner_sub_title_2.*' => 'required|max:50',
                'banner_image.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
            ],
            'notice' => [
                'banner_icon' => 'Upload a 48x48 pixel image for optimal image quality.',
                'banner_image' => 'Upload a 651x591 pixel image for optimal image quality.'
            ]
        ],
        'multiple' => [
            'field_name' => [
                'icon' => 'text',
                'title' => 'text',
                'short_description' => 'text',
            ],
            'validation' => [
                'icon.*' => 'required',
                'title.*' => 'required|max:60',
                'short_description.*' => 'required|max:250',
            ]
        ],
        'preview' => [
            'About Two' => 'assets/themes/light/images/sections/about_two.png',
        ]
    ],
    'why_choose_us_section' => [
        'single' => [
            'field_name' => [
                'title' => 'text',
                'sub_title' => 'text',
            ],
            'validation' => [
                'title.*' => 'required|max:50',
                'sub_title.*' => 'required|max:100',
            ]
        ],
        'multiple' => [
            'field_name' => [
                'image' => 'file',
                'title' => 'text',
                'short_description' => 'text',
            ],
            'validation' => [
                'image.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
                'title.*' => 'required|max:60',
                'short_description.*' => 'required|max:250',
            ],
            'notice' => [
                'image' => 'Upload a 52x52 pixel image for optimal image quality.'
            ]
        ],
        'preview' => [
            'Why Choose Us One' => 'assets/themes/light/images/sections/why_chose.png',
        ]
    ],
    'why_choose_us_section_two' => [
        'single' => [
            'field_name' => [
                'title' => 'text',
                'sub_title' => 'text',
            ],
            'validation' => [
                'title.*' => 'required|max:50',
                'sub_title.*' => 'required|max:100',
            ]
        ],
        'multiple' => [
            'field_name' => [
                'image' => 'file',
                'title' => 'text',
                'short_description' => 'text',
            ],
            'validation' => [
                'image.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
                'title.*' => 'required|max:60',
                'short_description.*' => 'required|max:250',
            ]
        ],
        'preview' => [
            'Why Choose Us Two' => 'assets/themes/light/images/sections/why_choose_two.png',
        ]
    ],
    'investment_process_section' => [
        'single' => [
            'field_name' => [
                'title' => 'text',
                'sub_title' => 'text',
                'description' => 'text',
                'image' => 'file',
            ],
            'validation' => [
                'title.*' => 'required|max:50',
                'sub_title.*' => 'required|max:100',
                'description.*' => 'required|max:500',
                'image.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
            ],
            'notice' => [
                'image' => 'Upload a 381x330 pixel image for optimal image quality.'
            ]
        ],
        'multiple' => [
            'field_name' => [
                'image' => 'file',
                'title' => 'text',
                'short_description' => 'text',
            ],
            'validation' => [
                'image.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
                'title.*' => 'required|max:60',
                'short_description.*' => 'required|max:300',
            ],
            'notice' => [
                'image' => 'Upload a 40x40 pixel image for optimal image quality.'
            ]
        ],
        'preview' => [
            'investment process' => 'assets/themes/light/images/sections/process.png',
        ]
    ],
    'investment_process_section_two' => [
        'single' => [
            'field_name' => [
                'title' => 'text',
                'sub_title' => 'text',
            ],
            'validation' => [
                'title.*' => 'required|max:50',
                'sub_title.*' => 'required|max:100',
            ],
            'notice' => [
                'image' => 'Upload a 381x330 pixel image for optimal image quality.'
            ]
        ],
        'multiple' => [
            'field_name' => [
                'title' => 'text',
                'short_description' => 'text',
            ],
            'validation' => [
                'title.*' => 'required|max:60',
                'short_description.*' => 'required|max:300',
            ]
        ],
        'preview' => [
            'investment process Two' => 'assets/themes/light/images/sections/process_two.png',
        ]
    ],
    'pricing_section' => [
        'single' => [
            'field_name' => [
                'title' => 'text',
                'sub_title' => 'text',
            ],
            'validation' => [
                'title.*' => 'required|max:50',
                'sub_title.*' => 'required|max:100',
            ]
        ],
        'preview' => [
            'Pricing One' => 'assets/themes/light/images/sections/pricing.png',
        ]
    ],
    'pricing_section_two' => [
        'single' => [
            'field_name' => [
                'title' => 'text',
                'sub_title' => 'text',
            ],
            'validation' => [
                'title.*' => 'required|max:50',
                'sub_title.*' => 'required|max:100',
            ]
        ],
        'preview' => [
            'Pricing Two' => 'assets/themes/light/images/sections/pricing_two.png',
        ]
    ],
    'counter_section' => [
        'multiple' => [
            'field_name' => [
                'countable_item_name' => 'text',
                'prefix' => 'text',
                'count' => 'number',
            ],
            'validation' => [
                'countable_item_name.*' => 'required|max:60',
                'prefix.*' => 'required|max:10',
                'count.*' => 'required|numeric',
            ]
        ],
        'preview' => [
            'Counter One' => 'assets/themes/light/images/sections/counter.png',
        ]
    ],
    'counter_section_two' => [
        'single' => [
            'field_name' => [
                'title' => 'text',
                'sub_title' => 'text',
            ],
            'validation' => [
                'title.*' => 'required|max:50',
                'sub_title.*' => 'required|max:100',
            ]
        ],
        'multiple' => [
            'field_name' => [
                'countable_item_name' => 'text',
                'prefix' => 'text',
                'count' => 'number',
            ],
            'validation' => [
                'countable_item_name.*' => 'required|max:60',
                'prefix.*' => 'required|max:10',
                'count.*' => 'required|numeric',
            ]
        ],
        'preview' => [
            'Counter Two' => 'assets/themes/light/images/sections/counter_two.png',
        ]
    ],
    'calculation_section' => [
        'single' => [
            'field_name' => [
                'title' => 'text',
                'sub_title' => 'text',
                'short_description' => 'text',
                'banner_title' => 'text',
                'banner_description' => 'text',
                'banner_button_name' => 'text',
                'banner_button_link' => 'url',
                'calculator_title' => 'text',
                'calculator_short_description' => 'text',
                'banner_image' => 'file'
            ],
            'validation' => [
                'title.*' => 'required|max:50',
                'sub_title.*' => 'required|max:100',
                'short_description.*' => 'required|max:500',
                'banner_title.*' => 'required|max:60',
                'banner_description.*' => 'required|max:300',
                'banner_button_name.*' => 'required|max:60',
                'banner_button_link.*' => 'nullable|max:60',
                'banner_image.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
                'calculator_title' => 'required|max:60',
                'calculator_short_description' => 'required|max:300',
            ],
            'notice' => [
                'banner_image' => 'Upload a 583x496 pixel image for optimal image quality.'
            ]
        ],
        'multiple' => [
            'field_name' => [
                'feature' => 'text'
            ],
            'validation' => [
                'feature.*' => 'required|max:100',
            ]
        ],
        'preview' => [
            'Calculation' => 'assets/themes/light/images/sections/calculator.png',
        ]
    ],
    'product_section' => [
        'single' => [
            'field_name' => [
                'title' => 'text',
                'sub_title' => 'text',
                'short_description' => 'text',
                'banner_title' => 'text',
                'banner_description' => 'text',
                'banner_button_name' => 'text',
                'banner_button_link' => 'url',
                'banner_image' => 'file'
            ],
            'validation' => [
                'title.*' => 'required|max:50',
                'sub_title.*' => 'required|max:100',
                'description.*' => 'required|max:200',
                'banner_title.*' => 'required|max:60',
                'banner_description.*' => 'required|max:300',
                'banner_button_url.*' => 'nullable',
                'banner_button_name.*' => 'required|max:60',
                'banner_image.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
            ],
            'notice' => [
                'banner_image' => 'Upload a 705x412 pixel image for optimal image quality.'
            ]
        ],
        'preview' => [
            'Product Section' => 'assets/themes/light/images/sections/product.png',
        ]
    ],
    'testimonial_section' => [
        'single' => [
            'field_name' => [
                'title' => 'text',
                'sub_title' => 'text',
            ],
            'validation' => [
                'title.*' => 'required|max:50',
                'sub_title.*' => 'required|max:100',
            ]
        ],
        'multiple' => [
            'field_name' => [
                'name' => 'text',
                'position' => 'text',
                'rating' => 'number',
                'review' => 'textarea',
                'image' => 'file',
            ],
            'validation' => [
                'name.*' => 'required|max:50',
                'position.*' => 'required|max:100',
                'rating.*' => 'required|numeric|max:5',
                'review.*' => 'required|max:500',
                'image.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
            ],
            'notice' => [
                'notice' => [
                    'image' => 'Upload a 446x416 pixel image for optimal image quality.'
                ]
            ]
        ],
        'preview' => [
            'Testimonial One' => 'assets/themes/light/images/sections/review.png',
        ]
    ],
    'testimonial_section_two' => [
        'single' => [
            'field_name' => [
                'title' => 'text',
                'sub_title' => 'text',
                'short_description' => 'text',
            ],
            'validation' => [
                'title.*' => 'required|max:50',
                'sub_title.*' => 'required|max:100',
                'short_description.*' => 'required|max:200',
            ]
        ],
        'multiple' => [
            'field_name' => [
                'name' => 'text',
                'position' => 'text',
                'rating' => 'number',
                'review' => 'textarea',
                'image' => 'file',
            ],
            'validation' => [
                'name.*' => 'required|max:50',
                'position.*' => 'required|max:100',
                'rating.*' => 'required|numeric|max:5',
                'review.*' => 'required|max:500',
                'image.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
            ],
            'notice' => [
                'notice' => [
                    'image' => 'Upload a 64x64 pixel image for optimal image quality.'
                ]
            ]
        ],
        'preview' => [
            'Testimonial Two' => 'assets/themes/light/images/sections/testimonial_two.png',
        ]
    ],
    'blog_section' => [
        'single' => [
            'field_name' => [
                'title' => 'text',
                'sub_title' => 'text',
                'button_name' => 'text',
                'button_url' => 'url',
            ],
            'validation' => [
                'title.*' => 'required|max:50',
                'sub_title.*' => 'required|max:100',
                'button_name.*' => 'required|max:60',
                'button_url.*' => 'nullable|max:60',
            ]
        ],
        'preview' => [
            'Blog One' => 'assets/themes/light/images/sections/blog.png',
        ]
    ],
    'blog_section_two' => [
        'single' => [
            'field_name' => [
                'title' => 'text',
                'sub_title' => 'text',
            ],
            'validation' => [
                'title.*' => 'required|max:50',
                'sub_title.*' => 'required|max:100',
            ]
        ],
        'preview' => [
            'Blog Two' => 'assets/themes/light/images/sections/blog_two.png',
        ]
    ],
    'faq_section' => [
        'single' => [
            'field_name' => [
                'title' => 'text',
                'sub_title' => 'text',
            ],
            'validation' => [
                'title.*' => 'required|max:50',
                'sub_title.*' => 'required|max:100',
            ]
        ],
        'multiple' => [
            'field_name' => [
                'question' => 'text',
                'answer' => 'textarea',
            ],
            'validation' => [
                'question.*' => 'required|max:150',
                'answer.*' => 'required|max:400',
            ]
        ],
        'preview' => [
            'FAQ' => 'assets/themes/light/images/sections/faq.png',
        ]
    ],
    'contact_section' => [
        'single' => [
            'field_name' => [
                'title' => 'text',
                'sub_title' => 'text',
                'email' => 'text',
                'phone' => 'text',
                'location' => 'text',
                'support' => 'text',
                'location_url' => 'url',
                'image' => 'file',
                'background_image' => 'file',
            ],
            'validation' => [
                'email.*' => 'required',
                'phone.*' => 'required|max:50',
                'location.*' => 'required|max:100',
                'support.*' => 'required|max:100',
                'location_url.*' => 'nullable',
                'title.*' => 'nullable|max:100',
                'sub_title.*' => 'nullable|max:100',
                'image.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
                'background_image.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
            ]
        ],
        'preview' => [
            'Contact' => 'assets/themes/light/images/sections/contact.png',
            'Map' => 'assets/themes/light/images/sections/map.png',
        ]
    ],
    'login_section' => [
        'single' => [
            'field_name' => [
                'title' => 'text',
                'sub_title' => 'text',
                'button_name' => 'text',
                'button_url' => 'url',
                'image' => 'file',
            ],
            'validation' => [
                'title.*' => 'required|max:50',
                'sub_title.*' => 'required|max:100',
                'button_name.*' => 'required|max:60',
                'button_url.*' => 'nullable|max:60',
                'image.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
            ],
            'notice' => [
                'image' => 'Upload a 916x1078 pixel image for optimal image quality.'
            ]
        ],
        'preview' => [
            'Login' => 'assets/themes/light/images/sections/login.png',
        ]
    ],
    'register_section' => [
        'single' => [
            'field_name' => [
                'title' => 'text',
                'sub_title' => 'text',
                'button_name' => 'text',
                'button_url' => 'url',
                'image' => 'file',
            ],
            'validation' => [
                'title.*' => 'required|max:50',
                'sub_title.*' => 'required|max:100',
                'button_name.*' => 'required|max:60',
                'button_url.*' => 'nullable|max:60',
                'image.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
            ],
            'notice' => [
                'image' => 'Upload a 916x1078 pixel image for optimal image quality.'
            ]
        ],
        'preview' => [
            'Register' => 'assets/themes/light/images/sections/register.png',
        ]
    ],
    'cookie_policy' => [
        'multiple' => [
            'field_name' => [
                'heading' => 'text',
                'description' => 'textarea',
            ],
            'validation' => [
                'heading.*' => 'required|max:80',
                'description.*' => 'required',
            ]
        ],
        'preview' => [
            'Cookie Policy' => 'assets/themes/light/images/sections/policy.png',
        ]
    ],
    'terms_condition' => [
        'multiple' => [
            'field_name' => [
                'heading' => 'text',
                'description' => 'textarea',
            ],
            'validation' => [
                'heading.*' => 'required|max:80',
                'description.*' => 'required',
            ]
        ],
        'preview' => [
            'Terms & Condition' => 'assets/themes/light/images/sections/policy.png',
        ]
    ],
    'privacy_policy' => [
        'multiple' => [
            'field_name' => [
                'heading' => 'text',
                'description' => 'textarea',
            ],
            'validation' => [
                'heading.*' => 'required|max:80',
                'description.*' => 'required',
            ]
        ],
        'preview' => [
            'Privacy Policy' => 'assets/themes/light/images/sections/policy.png',
        ]
    ],
    'footer_section' => [
        'single' => [
            'field_name' => [
                'short_description' => 'text',
                'phone' => 'text',
                'address' => 'text',
                'email' => 'text',
                'newsletter_title' => 'text',
                'newsletter_description' => 'text',
            ],
            'validation' => [
                'short_description.*' => 'required|max:100',
                'phone.*' => 'required',
                'address.*' => 'required|max:150',
                'email.*' => 'required|email',
                'newsletter_title' => 'required',
                'newsletter_description.*' => 'required|max:100',
            ]
        ],
        'multiple' => [
            'field_name' => [
                'icon' => 'text',
                'link' => 'url',
            ],
            'validation' => [
                'icon.*' => 'nullable',
                'link.*' => 'nullable',
            ]
        ],
        'preview' => [
            'Footer One' => 'assets/themes/light/images/sections/footer.png',
            'Footer Two' => 'assets/themes/light/images/sections/footer_two.png',
        ]
    ],

    'pwa_popup' => [
        'single' => [
            'field_name' => [
                'title' => 'text',
                'short_description' => 'text',
                'description' => 'text',
                'icon_image' => 'file'
            ],
            'validation' => [
                'title' => 'nullable',
                'short_description.*' => 'nullable',
                'description' => 'nullable',
                'icon_image.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
            ],
            'notice' => [
                'icon_image' => 'Please upload an image with dimensions of 48*48 pixels for the best quality.',
            ]
        ],
        'multiple' => [
            'field_name' => [
                'carousel_image' => 'file'
            ],
            'validation' => [
                'carousel_image.*' => 'required|max:3072|image|mimes:jpg,jpeg,png',
            ]
        ],
        'preview' => [
            'PWA' => 'assets/themes/light/images/sections/pwa.png',
        ]
    ],


    'message' => [
        'required' => 'This field is required.',
        'min' => 'This field must be at least :min characters.',
        'max' => 'This field may not be greater than :max characters.',
        'image' => 'This field must be image.',
        'mimes' => 'This image must be a file of type: jpg, jpeg, png.',
        'integer' => 'This field must be an integer value',
    ],

    'content_media' => [
        'image' => 'file',
        'powered_by' => 'file',
        'background_image' => 'file',
        'invest_card_image' => 'file',
        'brand' => 'file',
        'link' => 'url',
        'optional_link' => 'url',
        'button_url' => 'url',
        'banner_button_link' => 'url',
        'location_url' => 'url',
        'video_url' => 'url',
        'icon' => 'icon',
        'number_of_ratings' => 'number',
        'rating' => 'number',
        'count' => 'number',
        'start_date' => 'date',
        'reviewer' => 'file',
        'client_image_1' => 'file',
        'client_image_2' => 'file',
        'client_image_3' => 'file',
        'about_image_1' => 'file',
        'about_image_2' => 'file',
        'banner_image' => 'file',
        'banner_icon' => 'file',
        'icon_image' => 'file',
        'carousel_image' => 'file',
        'rotate_image' => 'file',
    ]

];
