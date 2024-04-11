<?php

	
require('autoload.php');



global $lumise;



$orderby  = '`order`';

$ordering = 'asc';

$dt_order = 'name_asc';

$current_page = isset($_GET['tpage']) ? $_GET['tpage'] : 1;



$search_filter = array(

    'keyword' => '',

    'fields' => 'name'

);



$default_filter = array(

    'type' => '',

);

$per_page = 8;

$start = ( $current_page - 1 ) * $per_page;

$data = $lumise->lib->get_rows('products', $search_filter, $orderby, $ordering, $per_page, $start, array('active'=> 1), '', 1);



include(theme('header.php'));



?>

        <div class="lumise-hero">

            <div class="owl-carousel owl-theme">

                <div class="item" style="background:url('<?php echo theme('assets/images/banner1.png', true); ?>') no-repeat;background-size:cover;">

                    <div class="container">

                        <h1><?php echo $lumise->lang('Best In Class Printing Solutions'); ?></h1>

                        <a href="<?php echo $lumise->cfg->url.'products.php'; ?>"><?php echo $lumise->lang('View Products'); ?></a>

                    </div>

                </div>

                <div class="item" style="background:url('<?php echo theme('assets/images/banner2.png', true); ?>')no-repeat;background-size:cover;">

                    <div class="container">

                        <h1><?php echo $lumise->lang('Exquisite Employee Welcome Kits!!'); ?></h1>

                        <a href="<?php echo $lumise->cfg->url.'products.php'; ?>"><?php echo $lumise->lang('View Products'); ?></a>

                    </div>

                </div>

            </div>

        </div>

        <div class="container">

            <div class="lumise-services">

                <div class="row">

                    <div class="col-md-3 col-sm-6">

                        <div class="box-info">

                            <i class="fa fa-truck" aria-hidden="true"></i>

                            <div class="content">

                                <h4><?php echo $lumise->lang('Fastest TATs'); ?></h4>

                                <p><?php echo $lumise->lang('On Time PAN India Delivery'); ?></p>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-3 col-sm-6">

                        <div class="box-info">

                            <i class="fa fa-refresh" aria-hidden="true"></i>

                            <div class="content">

                                <h4><?php echo $lumise->lang('Quality Guaranteed'); ?></h4>

                                <p><?php echo $lumise->lang('Client Satisfaction is Top Priority'); ?></p>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-3 col-sm-6">

                        <div class="box-info">

                            <i class="fa fa-credit-card" aria-hidden="true"></i>

                            <div class="content">

                                <h4><?php echo $lumise->lang('Secured Payments'); ?></h4>

                                <p><?php echo $lumise->lang('For both B2B &amp; B2C Clients'); ?></p>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-3 col-sm-6">

                        <div class="box-info">

                            <i class="fa fa-life-ring" aria-hidden="true"></i>

                            <div class="content">

                                <h4><?php echo $lumise->lang('Unmatched Support'); ?></h4>

                                <p><?php echo $lumise->lang('Dedicated Support Teams'); ?></p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <?php LumiseView::categories(); ?> 
        
                <div class="lumise-list">

            <div class="container">

                <h2><?php echo $lumise->lang('About Us'); ?></h2>

                <p align="justify">ColorFast is an integrated gifting and promotional products organization aiming to redefine the concept of
corporate gifting in India. Founded in 2023, Colorfast
aims to be known as a Quality Player in a highly
fragmented market. Our Motto is simple - Innovation,
Quality and Service. This allows us to ensure that each
customer has a unique experience in each engagement
with Colorfast.</p>

<p align="justify">The company is constantly innovating and thinking of
new and interesting products to offer our clients. Our
focus on building client relationships sets us apart from
being just another gifting company and our ability to
provide customers with tailor-made options to suit their
individual requirements at competitive prices makes us a
preferred partner. Colorfast has worked hard to reach the
present position, which would not have been possible
without our dedicated team of professionals and support
staff. The company is unique in its ability to offer an
extensive range of branded products at value-for-money
prices. Our experience also allows us to meet challenging
bulk orders and our widespread overseas network assures
excellent import services and on-time delivery.</p>


<div class="row">

                    <div class="col-md-2 col-sm-4">

                        <div class="client"><img src="<?php echo theme('assets/images/delivery1.png', true); ?>" alt=""></div>

                    </div>

                    <div class="col-md-2 col-sm-4">

                        <div class="client"><img src="<?php echo theme('assets/images/delivery2.png', true); ?>" alt=""></div>

                    </div>

                    <div class="col-md-2 col-sm-4">

                        <div class="client"><img src="<?php echo theme('assets/images/delivery3.png', true); ?>" alt=""></div>

                    </div>

                    <div class="col-md-2 col-sm-4">

                        <div class="client"><img src="<?php echo theme('assets/images/delivery4.png', true); ?>" alt=""></div>

                    </div>

                    <div class="col-md-2 col-sm-4">

                        <div class="client"><img src="<?php echo theme('assets/images/delivery5.png', true); ?>" alt=""></div>

                    </div>

                    <div class="col-md-2 col-sm-4">

                        <div class="client"><img src="<?php echo theme('assets/images/delivery6.png', true); ?>" alt=""></div>

                    </div>

                </div>

            </div>

        </div>

        <div class="lumise-list">

            <div class="container">

                <h2><?php echo $lumise->lang('Featured products'); ?></h2>

                <?php LumiseView::products($data['rows']); ?>

            </div>

        </div>

        <div class="lumise-list">

            <div class="container">
            
            <h2><?php echo $lumise->lang('Our Clients'); ?></h2>
            
            <p align="justify">Our Vision is to become a one-stop "unique" solution provider for all the printing and customization requirements of our clients through innovation and quality of service.</p>
            
            <p align="justify">We are a trusted and go-to partner for our clients for all B2B printing and corporate gifting requirements through delivering high quality and innovative products as well as providing a service
experience. Through our state-of-the-art machinery, experienced team and, above all, passion for creativity. We cater to our clients and assist in elevating their brand image.</p>
                <div class="row">

                    <div class="col-md-2 col-sm-4">

                        <div class="client"><img src="<?php echo theme('assets/images/logo1.png', true); ?>" alt=""></div>

                    </div>

                    <div class="col-md-2 col-sm-4">

                        <div class="client"><img src="<?php echo theme('assets/images/logo2.png', true); ?>" alt=""></div>

                    </div>

                    <div class="col-md-2 col-sm-4">

                        <div class="client"><img src="<?php echo theme('assets/images/logo3.png', true); ?>" alt=""></div>

                    </div>

                    <div class="col-md-2 col-sm-4">

                        <div class="client"><img src="<?php echo theme('assets/images/logo4.png', true); ?>" alt=""></div>

                    </div>

                    <div class="col-md-2 col-sm-4">

                        <div class="client"><img src="<?php echo theme('assets/images/logo5.png', true); ?>" alt=""></div>

                    </div>

                    <div class="col-md-2 col-sm-4">

                        <div class="client"><img src="<?php echo theme('assets/images/logo6.png', true); ?>" alt=""></div>

                    </div>

                </div>

            </div>

        </div>

<?php include(theme('footer.php')); ?>

