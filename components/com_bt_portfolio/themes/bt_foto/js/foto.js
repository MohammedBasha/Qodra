BTP=jQuery;
var dataFilter;
BTP(document).ready(function(){
	BTP('.iframe-hover,.title-hover').hide();
	BTP('.btp-list .btp-item a').hover(function(){
		var item = BTP(this);
		BTP('.iframe-hover,.title-hover',item).fadeToggle('fast');
		BTP('.iframe-hover-inner',item).css('height',(BTP('.image-default',item).height()- 8)+'px');
	});
	var $list = BTP('.btp-list');
	var $data = $list.clone();
	var $preferences = {
		    duration: 800,
		    easing: 'swing',
			useScaling: true,
		    adjustHeight: false,
		    adjustWidth: false
		  };
	BTP('.btp-categories a').click(function(){	
		var $dataFilter;
		BTP('.btp-categories a').removeClass('active');
		BTP(this).addClass('active');
		var value = BTP(this).attr('data-value');
		var title = BTP('span',this).html();
		var description = BTP('input',this).val();
		var titleObj = BTP('.btp-title').find('span');
		if(!titleObj.length) titleObj = BTP('.btp-title');
		titleObj.html(title);
		BTP('.btp-catdesc').html(description);
		
		if(value!='all'){
			values = value.split(',');
			for(i=0;i<values.length;i++){
				$dataFilter = $data.find('.'+value);
			}
		}
		else{
			$dataFilter = $data.find('.btp-item');
			title = BTP('span',this).attr('title');
			if(title) titleObj.html(title);
		}
		var stringDataId = '';
		$dataFilter.each(function(){
			var dataId = BTP(this).attr('data-id');
			if(stringDataId.indexOf(dataId)== -1){
				stringDataId += dataId + ',';
				BTP(this).addClass('getting');
				BTP(this).show();
			}
		});
		$dataFilter = $dataFilter.filter('.getting').removeClass('getting');
		
		$list.quicksand($dataFilter,$preferences,function(){
			BTP('.iframe-hover,.title-hover').hide();
			BTP('.iframe-hover-inner').css('height',(BTP('.image-default').height()- 8)+'px');
			BTP('.btp-item a').hover(function(){
				var item = BTP(this);
				BTP('.iframe-hover,.title-hover',item).fadeToggle('fast');
			});
		});		
		return false;
	});

	
	BTP('.btp-list').slideDown('slow');
	
	var stringDataId = '';
	BTP('.btp-list .btp-item:visible').each(function(){
		var dataId = BTP(this).attr('data-id');
		if(stringDataId.indexOf(dataId)== -1){
			stringDataId += dataId + ',';	
		}else{
			BTP(this).hide();
		}	
	});
});