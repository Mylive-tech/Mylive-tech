jQuery(document).ready(function($) {
	function setColor($element, setColorPickerColor) {
		var hashColor=$element.val();
		if(setColorPickerColor)
			$element.ColorPickerSetColor(hashColor);

		$element.css("backgroundColor",hashColor);
		var color=parseInt(hashColor.substr(1), 16);
		var colRed=color>>16;
		var colGreen=(color&0xFFFF)>>8;
		var colBlue=color&0xFF;

		var average = (colRed+colGreen+colBlue)/3;
		if(average<127)
			$element.css("color","#FFF");
		else
			$element.css("color","#000");
	}

	$('#THEME_BACKGROUND, #THEME_COLOR, #TWEET_BACKGROUND, #TWEET_COLOR, #TWEET_LINKS').each(function() {
		var $this=$(this);
		$this.ColorPicker({
			onSubmit: function(hsb, hex, rgb, el) {
				$(el).val('#'+hex);
				$(el).ColorPickerHide();
				setColor($(el), false);
			},
			onBeforeShow: function () {
				setColor($(this), true);
			},
			onChange: function(hsb, hex, rgb, el) {
				$this.val("#"+hex);
				setColor($this, false);
			}
		});

		var prevColor=null;
		function manualChange() {
			if(prevColor!==$this.val()) {
				prevColor=$this.val();

				//see if color code is valid
				if(prevColor.length==7 && prevColor.substr(0,1)=='#' && !isNaN(parseInt(prevColor.substr(1), 16))) {
					setColor($this, true);
				}
			}
		}

		$this.keyup(manualChange);
		$this.change(manualChange);

		setColor($this, true);
	});
});