<?php
$query_rlt = mysqli_query($mysqli, "SELECT * 
                                           FROM terms_and_conditions
                                           WHERE status != 'removed'");
?>

<div class="contact-us-area pt-100 pb-65">
    <div class="container">
        <div class="row align-items-center flex-row-reverse">
            <div class="col-lg-12">
                <div class="about-content">
                    <?php if (mysqli_num_rows($query_rlt)) {
                        $row = mysqli_fetch_array($query_rlt); ?>
                        <h4 data-aos="fade-up" data-aos-delay="200">
                            <?php echo $row['terms']; ?>
                        </h4>
                    <?php } else { ?>
                        <h4 data-aos="fade-up" data-aos-delay="200">
                            No Terms & Conditions
                        </h4>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
