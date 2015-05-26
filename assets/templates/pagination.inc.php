<?php
	if ($paginator->getNumPages() > 1) {
		echo '<ul class="pagination list-unstyled">';
			if ($paginator->getPrevUrl()) {
				echo '<li><a href="'.$paginator->getPrevUrl().'" data-block-id="'.$blockID.'" data-page="'.$paginator->getPrevPage().'">&laquo; Previous</a></li>';
			}
			
			foreach ($paginator->getPages() as $page) {
				if ($page['url']) {
					echo '<li '.($page['isCurrent'] ? 'class="active"' : '').'>
                    	<a href="'.$page['url'].'" data-block-id="'.$blockID.'" data-page="'.$page['num'].'">'.$page['num'].'</a>
					</li>';
				} else {
					echo '<li class="disabled"><span>'.$page['num'].'</span></li>';
				}				
			}
			
			if ($paginator->getNextUrl()) {
				echo '<li><a href="'.$paginator->getNextUrl().'" data-block-id="'.$blockID.'" data-page="'.$paginator->getNextPage().'">Next &raquo;</a></li>';
			}
		echo '</ul>
		<div class="generic-loader" style="display:none"><i class="fa fa-spin fa-spinner"></i> Loading...</div>';
	}
?>