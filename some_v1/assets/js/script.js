if ( localStorage.getItem('sent_data') )
{
	localStorage.removeItem('sent_data');
}

let
	changeMenuActived = false;

$('.menu .menu_do').on('click', function() {

	let
		sentData = {
			do_id: $(this).data('id'),
			[$(this).data('name')]: $(this).data('name')
		},
		allData  = {},
		$curElem = $(this).parents('.content').find('.text'),
		$parent  = $(this).parents('.item').eq(0);

	if ( sentData.in_menu )
	{
		console.log('%cЗапрос', 'background-color: #000; color: #51F7B3; padding: 3px 9px;');
		console.log(sentData);

		let
			confirmMove = confirm('Сделать "' + $curElem.text() + '" разделом ?');

		if ( confirmMove )
		{
			$.ajax({
				url: 'func/assets/menu.php',
				type: 'POST',
				data: sentData,
				success: function(res)
				{
					if ( (+res) === sentData.do_id )
					{
						location.reload();
					}
					else
					{
						alertMsg('Что-то пошло не так!');
					}
				}
			});
		}
	}
	else if ( sentData.edit_menu )
	{
		if ( !$curElem.hasClass('editable-active') )
		{
			let
				nowId = Math.floor(Math.random() * 1000000);

			$curElem.addClass('editable-active');
			$curElem.html('<input type="text" class="form-control" id="el' + nowId + '" placeholder="' + $curElem.text() + '">');
			$curElem.after('<button class="btn access_val btn-primary mr-2" data-id="el' + nowId + '">Изменить</button><button class="btn btn-danger">Отмена</button><div class="errors"></div>');
			
			$parent.children('.content').find('.btn-icons').removeClass('active');

			allData.id = 'el' + nowId;

			$('#el' + nowId).focus();
		}
	}
	else if ( sentData.del_menu )
	{
		if ( confirm('Удалить "' + $curElem.text() + '" ?') )
		{
			$.ajax({
				url: 'func/assets/menu.php',
				type: 'POST',
				data: sentData,
				success: function(res)
				{
					if ( (+res) === 1 )
					{
						$parent.remove();
					}
					else
					{
						alert('Не удалось удалить пункт меню!');
					}
				}
			});
		}
	}
	else if ( sentData.add_menu )
	{
		delete sentData.do_id;

		let
			$parent = $(this).parents('.item-add'),
			nowVal  = $parent.children('input').val().trim(),
			$errors = $parent.children('.errors');

		if ( nowVal != '' )
		{
			sentData.val = nowVal;

			$.ajax({
				url: 'func/assets/menu.php',
				type: 'POST',
				data: sentData,
				success: function(res)
				{
					if ( sentData.val == res )
					{
						location.reload();
					}
					else
					{
						$errors.html('<span class="error">' + res + '</span>');
					}
				}
			});
		}
		else
		{
			$errors.html('<span class="error">Заполните поле!</span>');
		}
	}
	else if ( sentData.add_sub )
	{
		if ( $parent.children('.submenu').children('.item.sub_add').length < 1 )
		{
			sentData.sub_place = sentData.do_id;

			delete sentData.do_id;

			let
				subNowId    = Math.floor(Math.random() * 1000000),
				subMenuHtml = '<div class="item ml-3 sub_add"><div class="content"><span class="pointer lni-chevron-down"></span><span class="text editable-active"><input type="text" class="form-control" id="el' + subNowId + '" placeholder="Подраздел: ' + $parent.children('.content').find('.text').text() + '"></span><button class="btn access_val btn-primary mr-2" data-id="el' + subNowId + '">Добавить</button><button class="btn btn-danger cancel_add_sub">Отмена</button><div class="errors"></div></div></div>',
				$curSubMenu = $parent.children('.submenu');

			allData.id = 'el' + subNowId;

			if ( $curSubMenu.length > 0 )
			{
				$curSubMenu.append(subMenuHtml);
			}
			else
			{
				$parent.append('<div class="submenu sub_add">' + subMenuHtml + '</div>');
			}

			if ( !$parent.hasClass('active') )
			{
				$parent.addClass('active');
			}

			$('#el' + subNowId).focus();
		}
	}
	else if ( sentData.change_menu )
	{
		let
			$allItems = $('.menu .item');

		for (let i = 0; i < $allItems.length; i++)
		{
			let
				$curItem = $allItems.eq(i);

			$curItem.children('.content').addClass('sel_menu')

			if ( !$curItem.hasClass('active') )
			{
				$curItem.addClass('active');
			}
		}

		$parent.children('.content').removeClass('sel_menu');

		sessionStorage.setItem('change_data', JSON.stringify(sentData));

		$('.menu .editable').trigger('click');

		changeMenuActived = true;

		alertMsg('Выберите раздел', 1500);
	}

	if ( !sentData.del_menu && !sentData.add_menu )
	{
		let
			curMenus = localStorage.getItem('sent_data');

		allData.data = sentData;

		if ( curMenus )
		{
			let
				curMenusArr = JSON.parse(curMenus);

			curMenusArr.push(allData);

			curMenus = JSON.stringify(curMenusArr);
		}
		else
		{
			curMenus = JSON.stringify([allData]);
		}

		localStorage.setItem('sent_data', curMenus);
	}

});

$('.menu .content').on('click', function() {

	if ( changeMenuActived === true && $(this).hasClass('sel_menu') )
	{
		let
			sentData = JSON.parse(sessionStorage.getItem('change_data')),
			idNew    = $(this).data('id');

		if ( sentData && idNew != sentData.sub_id )
		{
			let
				allowAppend = confirm('Перенести вместе с подразделами (если есть) ?');

			if ( allowAppend )
			{
				sentData.allow = 'allow';
			}

			sentData.sub_id = idNew;

			$.ajax({
				url: 'func/assets/menu.php',
				type: 'POST',
				data: sentData,
				success: function(res)
				{
					if ( (+res) == sentData.do_id )
					{
						location.reload();
					}
					else
					{
						alertMsg(res, 1500);
					}
				}
			});

			changeMenuActived = false;
		}		
	}	

});

$('.menu').on('click', '.access_val', function() {

	let
		curSentDataJson = localStorage.getItem('sent_data'),
		curSentData     = {},
		curElemId       = $(this).data('id'),
		$curValPlace    = $('#' + curElemId),
		$curParentElem  = $curValPlace.parents('.content'),
		$curErrors      = $curParentElem.children('.errors');

	if ( curSentDataJson && $curValPlace.length > 0 )
	{
		curSentData = JSON.parse(curSentDataJson);

		for (let i = 0; i < curSentData.length; i++)
		{
			if ( curSentData[i].id == curElemId )
			{
				let
					curData = curSentData[i].data,
					curVal  = $curValPlace.val().trim();

				if ( curVal != '' )
				{
					curData.val = curVal;

					$.ajax({
						url: 'func/assets/menu.php',
						type: 'POST',
						data: curData,
						success: function(res)
						{
							if ( curData.edit_menu )
							{
								if ( res == curData.val )
								{
									$curParentElem.find('.btn').remove();

									$curErrors.remove();

									$curParentElem.children('.text').removeClass('editable-active');

									$curParentElem.children('.text').text(res);

									$curParentElem.children('.btn-icons').addClass('active');
								}
								else
								{
									$curErrors.html('<span class="error">Введите другое значение!</span>');
								}
							}
							else if ( curData.add_sub )
							{
								if ( curData.val == res )
								{
									location.reload();
								}
								else
								{
									$curErrors.html('<span class="error">' + res + '</span>');
								}
							}
						}
					});
				}
				else
				{
					$curErrors.html('<span class="error">Заполните поле!</span>');
				}

				break;
			}
		}
	}

});

$('.menu .item .text').on('click', function() {

	if ( !$(this).hasClass('editable-active') )
	{
		$(this).parents('.item').eq(0).toggleClass('active');
	}

});

$('.menu .item').on('click', '.cancel_add_sub', function() {

	let
		$curSubs = $(this).parents('.submenu.sub_add').find('.item');

	$curSubs.children('.content').children('.errors').remove();

	if ( $curSubs.length < 2 && $curSubs.length > 0 )
	{
		$curSubs.remove();
	}
	else
	{
		$(this).parents('.item.sub_add').remove();
	}

});

$('.menu .content').on('click', '.btn-danger', function() {

	let
		$curParent = $(this).parents('.content');

	$curParent.children('.text').text($curParent.find('.text input').attr('placeholder'));

	$curParent.children('.text').removeClass('editable-active');

	$curParent.children('.btn-icons').addClass('active');

	$curParent.find('.btn').remove();

	$curParent.children('.errors').remove();

});

$('.menu .editable').on('click', function() {

	$(this).find('.indicator').toggleClass('off');

	$('.menu .btn-icons').toggleClass('active');

	$('.menu .form-elem').toggleClass('active');

	$('.submenu.sub_add').remove();
	$('.item.sub_add').remove();

	localStorage.removeItem('sent_data');

	$('.menu .item .errors').remove();
	$('.menu .item-add .errors').text('');

	for (let i = 0; i < $('.menu .content').length; i++)
	{
		let
			$curTextPlace = $('.menu .content').eq(i).children('.text');

		if ( $curTextPlace.hasClass('editable-active') )
		{
			$curTextPlace.text($curTextPlace.children('input').attr('placeholder'));

			$curTextPlace.removeClass('editable-active');

			$('.menu .content').eq(i).find('.btn').remove();

			if ( $(this).find('.indicator').hasClass('off') )
			{
				$('.menu .content').eq(i).find('.btn-icons').removeClass('active');
			}
		}
	}

});