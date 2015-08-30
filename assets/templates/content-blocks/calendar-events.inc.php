<?php
	/**
	 * EVENT Calendar
	 * from google calendar. 
	 * pulls current events in from google calendar(s)
	 * gets ajax'd in.
	 * with option to show list in addition to calendar view
	*/
	echo '<div class="cal-full-block" data-block-id="'.$content["blockID"].'" data-events-list="'.($optionsJson["showEventList"] == 1 ? 'true' : 'false').'">		
		<div class="cal-grid-view">
			<div class="cal-options" id="cal-options-'.$content["blockID"].'">
				<div class="cal-controls pull-right">
					<div class="btn-group">
						<button class="btn btn-default btn-sm" data-calendar-nav="prev"><i class="fa fa-chevron-circle-left"></i></button>
						<button class="btn btn-default btn-sm" data-calendar-nav="today">Today</button>
						<button class="btn btn-default btn-sm" data-calendar-nav="next"><i class="fa fa-chevron-circle-right"></i></button>
					</div>
				</div>
				<h3><i class="fa fa-spin fa-spinner"></i> Loading&hellip;</h3>
			</div>
			<div id="calendar-load-'.$content["blockID"].'" ></div>
			<p class="help-block">Click a calendar cell above to view its events.</p>
		</div>
		
		<div class="cal-list-view">
			<div class="widget-header">Events for the Month</div>
			<div class="cal-list-view-inner">
				<div id="events-list-month-'.$content["blockID"].'"></div>
				<div id="ev-list-helper-'.$content["blockID"].'" class="ev-helper-wrap">
					<div id="events-list-'.$content["blockID"].'" class="events-list-wrap"></div>
					<div class="helper" style="display:none"><i class="fa fa-arrow-circle-down"></i></div>
				</div>
				<div id="ev-pg-contain-'.$content["blockID"].'" class="pagination-contain"></div>
			</div>
			
			<div class="modal fade" id="events-detail-modal">
			    <div class="modal-dialog">
			        <div class="modal-content">
			            <div class="modal-header">
			                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			                <h3>Event</h3>
			            </div>
			            <div class="modal-body"></div>
			            <div class="modal-footer">
			                <a href="#" data-dismiss="modal" class="btn btn-default">Close</a>
			            </div>
			        </div>
			    </div>
			</div>
		</div>
	</div>';
?>