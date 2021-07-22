document.querySelector('body').insertAdjacentHTML('beforeEnd', '<div class="msgs"></div>');

function alertMsg(text, time)
{
	let
		msgHtml = document.createElement('div');
	
	msgHtml.classList.add('msg');
	msgHtml.innerText = text;

	if ( time && time > 100 )
	{
		setTimeout(function() {
			msgHtml.remove();
		}, time);
	}

	document.querySelector('.msgs').appendChild(msgHtml);
}