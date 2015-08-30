<?php
	/**
	 * BOOK LIST
	 * display list of books based on Amazon book search API
	 * several display options (gallery / list etc)
	*/
	
	if(!$optionsJson["booksPerRow"]) $optionsJson["booksPerRow"] = 4;
	if(!$optionsJson["displayStyle"]) $optionsJson["displayStyle"] = 'bookshelf';
	$bookColumns = 12 / $optionsJson["booksPerRow"];
	
	$books = getPageContentBlockItemsData($content["blockID"]);
	if(count($books) > 0) {
		if($optionsJson["displayStyle"] == 'booklist') {
			foreach($books as $contentID=>$book) {
				$dataJSON = json_decode($book["dataJSON"], true);
				$reviewText = $dataJSON["reviewText"];
				$reviewText = $smarty->transform($reviewText); //smarty
				$reviewText = $parser->text($reviewText); //markdown parser
				echo '<div class="row">
					<div class="book-list-display clearfix">
						<div class="col-sm-4">
							<a href="'.$dataJSON["link"].'" target="_blank" class="thumbnail">
								<img src="'.$dataJSON["imgUrl"].'" class="img-responsive">
							</a>
							<div class="small text-muted">
								<a href="'.$dataJSON["link"].'" target="_blank">
									view on Amazon
								</a>
							</div>
						</div>
					
						<div class="col-sm-8 book-info">
							<h3 class="bk-title">'.$dataJSON["title"].'</h3>
							<h4 class="bk-author">'.$dataJSON["author"].'</h4>';
							if(!empty($dataJSON["submittedBy"])) echo '<p>Review by '.$dataJSON["submittedBy"].'</p>';
							echo $reviewText;
						echo '</div>
						<div class="col-sm-12 separator"><hr></div>
					</div>
				</div>';
			}
		} else {
			echo '<div class="row"><div class="book-gallery-display">';
				foreach($books as $contentID=>$book) {
					$dataJSON = json_decode($book["dataJSON"], true);
					if(is_array($dataJSON)) {
						$reviewText = $dataJSON["reviewText"];
						$reviewText = $smarty->transform($reviewText); //smarty
						$reviewText = $parser->text($reviewText); //markdown parser
						echo '<div class="'.($optionsJson["booksPerRow"] > 1 ? 'col-xs-6': '').' col-sm-'.$bookColumns.' book-item" data-content-id="'.$contentID.'">
							<a href="#" class="thumbnail" data-toggle="modal" data-target="#book-modal-'.$contentID.'">
								<img src="'.$dataJSON["imgUrl"].'" class="img-responsive">
								<span class="icon-overlay">
									<i class="fa fa-search"></i>
								</span>
							</a>
						</div>';
						
						//output the book info modal
						echo '<div class="modal fade" id="book-modal-'.$contentID.'" tabindex="-1" role="dialog" aria-labelledby="modalLabel-'.$contentID.'" aria-hidden="true">
							<div class="modal-dialog modal-lg">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title" id="modalLabel-'.$contentID.'">Book Details</h4>
									</div>
									<div class="modal-body">
										<div class="row">
											<div class="col-md-4">
												<a href="'.$dataJSON["link"].'" target="_blank" class="thumbnail">
													<img src="'.$dataJSON["imgUrl"].'" class="img-responsive">
												</a>
												<div class="small text-muted">
													<a href="'.$dataJSON["link"].'" target="_blank">
														view on Amazon
													</a>
												</div>
											</div>
											<div class="col-md-8 book-info">
												<h3 class="bk-title">'.$dataJSON["title"].'</h3>
												<h4 class="bk-author">'.$dataJSON["author"].'</h4>';
												if(!empty($dataJSON["submittedBy"])) echo '<p>Review by '.$dataJSON["submittedBy"].'</p>';
												echo $reviewText;
											echo '</div>
										</div>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
									</div>
								</div>
							</div>
						</div>';
					}
				}
			echo '</div></div>';
		}
	}
?>