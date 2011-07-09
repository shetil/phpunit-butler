SearchField = function(){
        this.input; 
        this.url = 'search.php/';
	this.mouseover = false;
	this.results = false;
	this.input = null;
        this.resultWrapper;
        this.curIndex = -1;
        this.charTracker = 0;
        this.els;

	this.onSearch = function(response,request){
            this.showThrobber(false);
            
            this.entries = Ext.decode(response.responseText);
            this.curIndex = -1;
            this.resultWrapper.dom.innerHTML = null;
            this.els = new Array();
            
            Ext.each(this.entries,function(entry){
                this.els.push(
                    Ext.get(
                    Ext.DomHelper.append(this.resultWrapper,{tag: 'li',html:entry})
                ));
            },this);
	};

        this.navSearchList = function(event){
            var nextIndex = null;

            if(this.resultWrapper.isVisible() == false){
                this.showResult();
                this.search();
                return;
            }

            if(event.keyCode == 40){
                nextIndex = this.curIndex + 1;
            } else {
                nextIndex = this.curIndex - 1;
            }

            if(nextIndex > this.entries.length -1 || nextIndex < 0){
                return false;
            }

            if(this.curIndex > -1){
                this.els[this.curIndex].removeClass('active');
            }
            this.els[nextIndex].addClass('active');
            
            this.input.dom.value = this.els[nextIndex].dom.innerHTML;
            this.curIndex = nextIndex;
        };

        this.onKeyUp = function(event){
            
            switch(event.keyCode){
                //Up or down key
                case 38:
                case 40:
                    this.navSearchList(event);
                    break;
                //Return key
                case 13:
                    this.submit();
                    break;
                default:
                    this.charTracker++;
                    this.search.defer(300,this);
            }

        };

	this.search = function(){
                this.charTracker--;
                if(this.charTracker > 0) return false;

                var str = this.input.dom.value;
		str.toLowerCase();
		str.replace(/^\s+|\s+$/g, '');

		this.showResult();

       this.showThrobber();
                
       
		Ext.Ajax.request({
			   url: this.url+str,
               params: {str: str},
			   success:  this.onSearch,
			   disableCaching: false,
			   scope: this
			});
	};

        this.showResult = function(){
            var pos = this.input.getXY();
                
            this.resultWrapper.setVisible(true);
            this.resultWrapper.setLeft(pos[0]);
            this.resultWrapper.setTop(pos[1] + this.input.getHeight());
            this.resultWrapper.setWidth(this.input.getWidth());
        }

	this.onMouseOut = function(e){
		var tg = e.getTarget();

		if(!Ext.isIE){
			if (tg.id != 'search-result'){
				e.stopPropagation();
				return;
			}

			if(this.mouseover == false){
				this.mouseover = true;
				return;
			}
		}

		this.hideSearchResult();
	};

	this.onFocus = function(e){
		if(this.results == true){
			this.mouseover = false;
			this.resultWrapper.setVisible(true);
		}
		e.stopPropagation();
	};

	this.hideSearchResult = function(e){
		this.mouseover = false;
		this.resultWrapper.setVisible(false);
	};

	this.init = function(form,input){
                this.form = form;
                this.input = Ext.get(input);

	  	this.input.on({
			'keyup' : {
                            fn: this.onKeyUp,
                            scope: this
			},
			'change' : {
				fn: this.search,
				buffer: 100,
				scope: this
			},
			'focus' : {
				fn: this.onFocus,
				scope: this
			}
		});

                this.resultWrapper = Ext.get(Ext.DomHelper.append(Ext.getBody(),{tag: 'ul',id: "search-result"}));
                this.resultWrapper.setVisibilityMode(Ext.Element.Display);

                this.resultWrapper.on('mouseout',this.onMouseOut,this);
		Ext.fly(Ext.getBody()).on('mouseover',this.hideSearchResult,this);

//		Ext.fly('search').on('mouseover',this.onFocus,this);
	};

        this.showThrobber = function(show){

            if(show == null || show == true){
                this.input.addClass('throbber');
            } else {
                this.input.removeClass('throbber');
            }
        }

        this.submit = function(){
            this.form.submit();
        }
}