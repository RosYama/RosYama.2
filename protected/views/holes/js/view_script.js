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
	
jQuery("#request-form .fileButtons a.downloadPdf").live("click",function() {
				jQuery.ajax({"type":"POST","beforeSend":function(){
									 },
				 "complete":function(){				 
					},"url":'/holes/GetRequestFile/'+$(this).attr('hole_id'),
					"cache":false,
					"data":$(this).parents("form").serialize(),
				"success":function(html){
					window.open(html, "printwindow", "width=800,height=600,location=no,toolbars=no,status=no,menubar=no, scrollbars=1");
					return false;
					}
				});			
				return false;
			});		