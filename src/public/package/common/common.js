
//requires jQuery
var common ={

	mask:{
		id:'__mask_for_loading',
		on:false,
		up:function(con){
			if(common.mask.on) return;
			common.mask.on = true;
			var w=$(window).width();
			var h1=$(window).height();
			//var h2=$(document).height();
			//var h = Math.max(h1, h2);
			var h = h1;
			var loader = con ? '' : '<div style="margin-top:'+(h/2)+'px;margin-left:'+(w/2-16)+'px" class="loader"></div>' ;
			var opacity = con ? "1" : "0.7"
			var msg = con ? '<b class="blinking" style="line-height:4px;font-weight:400;color:#fff"><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>'+(con || '')+'</b>' : '';
			var html = '<div id="'+common.mask.id+'" style="z-index:500;top:0;left:0;width:'+w+'px;height:'+h+'px;position:fixed; opacity:'+opacity+';background:#444;text-align:center" >'+loader+msg+'</div>' ;
			$('body').append(html)
			$('#'+common.mask.id).width();
			return true;
		},
		down:function(){
			common.mask.on = false;
			$('#'+common.mask.id).remove();
		}

	},
	time_bar:{
			count: 0,
			width : 0,
			tick : 60,
			timer : 30000,
			handler:null,
			selector:'#time-bar',
			cursor:'#time-bar-display',
			f: function(){common.mask.up();  $(common.time_bar.selector).attr('id', 'pass0000').css({height:'4px',background:'red'}).html(' '); setTimeout(function(){ window.location.reload();}, 10); },
			init:function(sel){
				sel = sel || '#time-bar';
				common.time_bar.selector = sel;
				if(!$(sel) ) return;
				var e = $(sel);
				common.time_bar.timer = e.attr('data-time') * 1000;
				common.time_bar.width = e.width();
				if(e.attr('data-func'))
					common.time_bar.f = e.attr('data-func');
				e.css({background: '#ddd', height:'2px'}).html('<div id="time-bar-display" style="width:0;height:2px;background:#00f;"></div>');
				common.time_bar.bar();
			},
			bar: function(func){
				if(common.time_bar.count++ >= common.time_bar.timer/common.time_bar.tick -1 ) {
					setTimeout(common.time_bar.f, 1);
					common.time_bar.count = 0;
				}
				$(common.time_bar.cursor).css({'width': common.time_bar.count * common.time_bar.width * common.time_bar.tick / common.time_bar.timer + "px"});
				common.time_bar.start();
			},
			start:function(){
				common.time_bar.handler = setTimeout(common.time_bar.bar, common.time_bar.tick);
			},
			stop:function(){
				clearTimeout(common.time_bar.handler);
			}
	},
	sound:{	//AV alerts
		_a:false ,
		_alert:{ok:{frequency:2120,duration:200}, alarm:{frequency:900,duration:200}, error:{frequency:390, duration:450}, ping:{frequency:[555, 555], duration:[200, 140]}, keyin:{frequency:15, duration:35}},
		_vol:600,
		ok:()=>{
			//	navigator.vibrate(app._alert.error.duration);
			common.sound.beep(common.sound._vol,common.sound._alert.ok.frequency,common.sound._alert.ok.duration);
			return true;
		},
		alarm:()=>{
			common.sound.beep(common.sound._vol,common.sound._alert.alarm.frequency,common.sound._alert.ok.duration);
				return true;
			},
		error: function(){
			$( "body" ).effect( "highlight");
			common.sound.beep(common.sound._vol, common.sound._alert.error.frequency,common.sound._alert.error.duration);
			return false;
		},
		ping: function(stop){
			var v = stop ? 1 :0;
			common.sound.beep(common.sound._vol, common.sound._alert.ping.frequency[v],common.sound._alert.ping.duration[v]);
			if(!stop) setTimeout(function(){ common.sound.alert.ping(1); }, 430);
			return false;
		},
		keyin: function(){
			common.sound.beep(common.sound._vol, common.sound._alert.keyin.frequency,common.sound._alert.keyin.duration);
			return false;
		},
		beep:(vol, freq, duration)=>{
			var a = common.sound._a || new AudioContext();
			var v=a.createOscillator()
			var u=a.createGain()
			v.connect(u)
			v.frequency.value=freq
			v.type="sine"
			u.connect(a.destination)
			u.gain.value=vol*0.01
			v.start(a.currentTime)
			v.stop(a.currentTime+duration*0.001)
		},
	},
	typing:(sel, con)=>{
		if(con.length){
			var s = con.charAt(0); con = con.replace(/^./g, '');
			$(sel).append(s);
			snd = $(sel).attr('data-snd') || 1
			if(snd && s != ' ') common.sound.keyin();
			if(s=="\n") {
				$(sel).append(' ');
				window.scrollTo(0, document.body.scrollHeight);
				$(sel)[0].scrollTop = $(sel)[0].scrollHeight;
			}
			// rt = common.random(10,200);
			rt = common.ND(1, 250);
			rt = rt - 100;
			rt = rt < 1 ? common.random(1, 20) : rt;
			$(sel).attr('data-snd', rt > 40);
			setTimeout(function(){common.typing(sel, con);}, rt);
		}	
	},
	random : (min, max)=> {
	  return Math.floor(Math.random() * (max - min)) + min;
  	},
	ND:(start, end)=>{
		start = start || 0
		end = end || 1
		let u = 0, v = 0;
		while(u === 0) u = Math.random(); //Converting [0,1) to (0,1)
		while(v === 0) v = Math.random();
		let num = Math.sqrt( -2.0 * Math.log( u ) ) * Math.cos( 2.0 * Math.PI * v );
		num = num / 10.0 + 0.5; // Translate to 0 -> 1
		if (num > 1 || num < 0) return common.ND(start, end); // resample between 0 and 1
		return parseInt(start+num*end);
	}

}

function _d(a){
	console.log(a);
}
