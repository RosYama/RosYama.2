			jQuery("#selectAll").live("click",function() {
				var sel_holes = new Array;
				var del;
				var i = 0;
				if ($(this).attr("checked")){
					del=false;
					$("#holes_list").find(".hole_check").each(function() {
						$(this).attr("checked",true);
						sel_holes[i]=$(this).val();
						i++;
						
					});	
				}				
				else {
					del=true;
					$("#holes_list").find(".hole_check").each(function() {
						$(this).removeAttr("checked");
						sel_holes[i]=$(this).val();
						i++;
					});					
				}
				selectHoles(sel_holes,del);				
			});
			
			jQuery(".state_block:not(.checked) .state_check").live("click",function() {
				var sel_holes = new Array;
				var i = 0;
				$(this).parents("div.state_block").toggleClass("checked")
				$(this).parent().next("ul").find(".hole_check").each(function() {
							     			if (!$(this).attr("checked")){
							     				$(this).attr("checked",true);
							     				sel_holes[i]=$(this).val();
							     				i++;
							     			}
							    	});
				selectHoles(sel_holes,false);			    	
			});
			
			jQuery(".state_block.checked .state_check").live("click",function() {
				var sel_holes = new Array;
				var i = 0;
				$(this).parents("div.state_block").toggleClass("checked")
				$(this).parent().next("ul").find(".hole_check").each(function() {
							     			if ($(this).attr("checked")){
							     				$(this).removeAttr("checked");
							     				sel_holes[i]=$(this).val();
							     				i++;							     				
							     			}
							    	});
				selectHoles(sel_holes,true);									    	
			});
			
			function checkInList(){
				var all=0; var checked=0;	
				$("#holes_list").find(".hole_check").each(function() {
							if ($(this).attr("checked")){
								checked++;
							}
						all++;	
					});	  											     			
				
				if (all==checked) {
					$("#selectAll").attr("checked",true);
					}	
				else {
					$("#selectAll").removeAttr("checked");
				}	
			}
			
			function checkInState(obj){
				var all=0; var checked=0;	
				obj.find(".hole_check").each(function() {
							if ($(this).attr("checked")){
								checked++;
							}
						all++;	
					});	  											     			
				
				if (all==checked) {
					obj.parent().find(".state_check").attr("checked",true);
					obj.parents(".state_block").toggleClass("checked");
					}	
				else {
					obj.parent().find(".state_check").removeAttr("checked");
					obj.parents(".state_block").removeClass("checked");
				}	
			}
			
			jQuery(".hole_check").live("click",function() {
				var del;
				if ($(this).attr("checked")) del=false;
				else del=true;
				selectHoles($(this).val(),del);
				checkInList();
			});		
			
			jQuery(".clear_selected").live("click",function() {
				selectHoles("all",true);
				
				$(".holes_list").each(function() {  
					$(this).find(".hole_check").each(function() {
							$(this).removeAttr("checked");	
					});	
					$(this).parent().find(".state_check").removeAttr("checked");
					$(this).parents(".state_block").removeClass("checked");
					});
					
				return false;
			});
			
			jQuery("a.save_selected").live("click",function() {
				jQuery.ajax({"type":"POST","beforeSend":function(){
					$("#holes_select_list").empty();
					$("#holes_select_list").addClass("loading");
				 },
				 "complete":function(){
				 $("#holes_select_list").removeClass("loading");
					},"url":$(this).attr("href"),"cache":false,
				"success":function(html){
					jQuery("#holes_select_list").html(html);
					}
				});			
				return false;
			});	
			
			jQuery("a.show_form").live("click",function() {
				jQuery.ajax({"type":"POST","beforeSend":function(){
					$("#pdf_form").hide();		
				 },
				 "complete":function(){
					$("#pdf_form").show();
					},"url":$(this).attr("href"),"cache":false,
				"success":function(html){
					jQuery("#gibdd_form").html(html);
					}
				});				
				return false;
			});	
			
			jQuery("#holes_selectors select").live("change",function() {
				$.fn.yiiListView.update("holes_list",{ data:$(this).parents("form").serialize()});								    	
			});