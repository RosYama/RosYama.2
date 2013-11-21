var map_big = false;
var map2    = null;

function toggleMap()
{
	if(map_big)
	{
		makeMapSmall();
	}
	else
	{
		makeMapBig();
	}
	map_big = !map_big;
	if($('#col').height()<$('#ymapcontainer_big').height())
	{
		$('#col').css('marginBottom',200)
	}
}

function makeMapBig()
{
	var a = document.getElementById('ymapcontainer_big');
	a.style.display = 'block';
	if(!map2)
	{
		map2 = new YMaps.Map(YMaps.jQuery("#ymapcontainer_big_map")[0]);
		YMaps.Events.observe(map2, map2.Events.DblClick, function () { toggleMap(); } );
		map2.enableScrollZoom();
		map2.setCenter(new YMaps.GeoPoint(map_centerx, map_centery), 15);
		var placemark = new YMaps.Placemark(new YMaps.GeoPoint(map_centerx, map_centery), { hideIcon: false, hasBalloon: false, style: s } );
		map2.addOverlay(placemark);
		YMaps.Events.observe(placemark, placemark.Events.Click, function () { toggleMap(); } );
	}
}

function makeMapSmall()
{
	var a = document.getElementById('ymapcontainer_big');
	a.style.display = 'none';
}

function selectAll(obj) {
  obj.focus()
  obj.select()
}

function gibddre_img_del(hole_id, img_id)
{
	jQuery.get
	(
		'/personal/edit.php',
		{
			DELETE_GIBDDRE_IMG: hole_id,
			deletefiles: 'gr' + img_id + '.jpg',
			ajax: 1
		},
		function(data)
		{
			if(data == 'ok')
			{
				$('#gibddreimg_' + img_id).fadeOut();
			}
			else
			{
				alert(data);
			}
		}
	);
}

jQuery(".delpicture").live("click",function(){
		return confirm('Вы уверены, что хотите удалить изображение?');
	});
	
jQuery("a.show_form_inhole").live("click",function() {
				if (!$("#pdf_form").hasClass('loaded')){
					jQuery.ajax({"type":"POST","beforeSend":function(){
						$("#pdf_form").hide();		
					 },
					 "complete":function(){
						$("#pdf_form").show();
						},"url":$(this).attr("href"),"cache":false,
					"success":function(html){
						jQuery("#gibdd_form").html(html);
						$("#pdf_form").addClass('loaded');
						}
					});				
				}
				else $("#pdf_form").toggle();
				return false;
			});		