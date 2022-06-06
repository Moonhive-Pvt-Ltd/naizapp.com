<div class="pagination-style-1 aos-init aos-animate" data-aos="fade-up" data-aos-delay="200">
    <ul class="pagination pagination-sm m-0">
        <li>
            <?php if ($pagenum > 1) { ?>
                <a class="pagination-btn-click" page-no="<?php echo $pagenum - 1; ?>">
                    <i class="ti-angle-double-left"></i>
                </a>
            <?php }  ?>
        </li>
        <li>
            <a <?php if ($pagenum - 1 <= 0 || $total_pages == 0) echo 'hidden' ?>
                    class="pagination-btn-click" page-no="<?php echo $pagenum - 1; ?>">
                <?php echo $pagenum - 1; ?>
            </a>
        </li>
        <li>
            <a <?php if ($pagenum > $total_pages || $total_pages == 0) echo 'hidden' ?>
                    class="pagination-btn-click active" page-no="<?php echo $pagenum; ?>">
                <?php echo $pagenum; ?>
            </a>
        </li>
        <li>
            <a <?php if ($pagenum + 1 > $total_pages || $total_pages == 0) echo 'hidden' ?>
                    class="pagination-btn-click" page-no="<?php echo $pagenum + 1; ?>">
                <?php echo $pagenum + 1; ?>
            </a>
        </li>
        <li>
            <a <?php if ($pagenum + 2 > $total_pages || $total_pages == 0) echo 'hidden' ?>
                    class="pagination-btn-click" page-no="<?php echo $pagenum + 2; ?>">
                <?php echo $pagenum + 2; ?>
            </a>
        </li>
        <li>
            <?php if ($pagenum == $total_pages || $total_pages == 0) { ?>
            <?php } else { ?>
                <a class="pagination-btn-click" page-no="<?php echo $pagenum + 1; ?>">
                    <i class="ti-angle-double-right"></i>
                </a>
            <?php } ?>
        </li>
    </ul>
</div>

<input type="hidden" class="page-number" value="<?php echo $pagenum; ?>">
