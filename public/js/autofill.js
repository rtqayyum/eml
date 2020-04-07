$.fn.autofill = function(action, param) {
    var box = this;    
    box.input = box.find('.autofill-input');
    box.value = function() {
        return box.input.val().trim();
    };
    box.error = box.input.attr('error-message');
    box.empty = box.input.attr('empty-message');
    box.header = box.input.attr('header');
    
    box.url = box.input.attr('data-url');
    box.current = null;
    box.xhr = null;
    
    // dropdown list
    box.loadingRow = '<li class="loader-box"><a class="loader" href="javascript:;"><div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div></a></li>';
    dropboxHtml = '<div class="autofill-dropbox-container hide"><ul class="autofill-dropbox">' +
        //'<li><a class="loading" href="javascript:;">'+loadingEffect+'</a></li>' +
        //'<li><a class="active" href="javascript:;"><label>Lawe Pham</label><span class="text-bold">L</span>awepham@gmail.com</a></li>' +
        //'<li><a class="" href="javascript:;"><label>Louis Walker</label><span class="text-bold">L</span>ouis@gmail.com</a></li>' +
        //'<li><a class="" href="javascript:;"><label>Lion Meo</label><span class="text-bold">L</span>ion@yahoo.com</a></li>' +
        //'<li><a class="" href="javascript:;"><label>****@gmail.com</label></a></li>' +
        //'<li><a class="" href="javascript:;"><label>****@abc.com</label></a></li>' +
        '</ul></div>';
    box.append(dropboxHtml);
    box.dropbox = box.find('.autofill-dropbox');
    box.container = box.find('.autofill-dropbox-container');
    
    if (box.header != '') {
        box.container.prepend('<h5 class="header">'+box.header+'</h5>');
    }
    
    //// Add loading row
    //box.dropbox.html(box.loadingRow);
    
    // ---- BEGIN FUNCTIONS ------------------------------
    // Init dropbox
    clearDropbox = function() {
        box.dropbox.html(box.loadingRow);
    };
    
    maskDropbox = function() {
        if (!box.dropbox.find('.loader-box').length) {
            box.dropbox.prepend(box.loadingRow);
        }
    };
    
    unmaskDropbox = function() {
        box.dropbox.find('.loader-box').remove();
    };
    
    // Show dropbox
    showDropbox = function() {
        if (box.value() != '') {
            box.container.removeClass('hide');
        }
    };
    
    // Hide dropbox
    hideDropbox = function() {
        box.container.addClass('hide');
    };
    
    // Toggle dropbox
    toggleDropbox = function() {
        if (box.value() != '') {
            showDropbox();
        } else {
            hideDropbox();
        }
    };
    
    // Set current
    setCurrent = function(li) {
        box.current = li;
            
        box.dropbox.find('li').removeClass('current');
        box.current.addClass('current');
    };
    
    // Reset current
    resetCurrent = function() {
        if (box.dropbox.find('li.autofill-item').length) {
            setCurrent(box.dropbox.find('li.autofill-item').first());
        } else {
            box.current = null;
        }
    };
    
    // Select current
    selectCurrent = function() {
        if (box.current != null) {
            var value = box.current.attr('data-value');
            var subfix = box.current.attr('data-subfix');
            if (value != 'undefined') {
                box.input.val(value);
            }
            if (subfix != 'undefined') {
                box.input.val(box.value().split('@')[0]+'@'+subfix);
            }
            box.input.blur();
        }
    };
    
    updateErrorMessage = function() {
        var valid = false;
        
        box.dropbox.find('li.autofill-item').each(function() {
            var dataValue = $(this).attr('data-value').trim().toLowerCase();
            var dataSubfix = $(this).attr('data-subfix').trim().toLowerCase();
            
            // sender
            if (typeof(box.value()) != 'undefined' && box.value().trim().toLowerCase() == dataValue) {
                valid = true;
                return;
            }
            
            // domain
            if (box.value().split('@').length >= 2 && box.value().split('@')[1].trim().toLowerCase() == dataSubfix) {
                valid = true;
                return;
            }
        });
        
        if (!valid && box.error != '') {
            if (!box.container.parent().find('.autofill-error').length) {
                box.container.after('<span class="helper-block autofill-error text-danger">'+box.error+'</span>');
            }
        } else {
            box.container.parent().find('.autofill-error').remove();
        }
    };
    
    renderDropboxList = function(data) {
        box.dropbox.html('');
        
        if (data.length) {
            data.forEach(function(row) {
                if (typeof(row.value) != 'undefined' || typeof(row.subfix) != 'undefined') {
                    var html = '<li class="autofill-item" data-value="'+row.value+'" data-subfix="'+row.subfix+'">' +
                        '<a href="javascript:;" class="autofill-item-a">' +
                            '<label>'+row.text+'</label>';
                    
                    if (row.desc != null) {
                        html += row.desc;
                    }
                    
                    html += '</a>' +
                        '</li>';
                        
                    box.dropbox.append(html);
                }
                
                if (typeof(row._warning) != 'undefined') {
                    var html = '<li class="">' +
                        '<span href="javascript:;" class="autofill-item-a">' +
                            '<label class="text-danger">'+row._warning+'</label>';
                    
                    html += '</span>' +
                        '</li>';
                        
                    box.dropbox.append(html);
                }
            });
        } else {
            var html = '<li class="">' +
                '<a class="autofill-item-empty text-center" href="javascript:;">' +
                    '<label>'+box.empty+'</label>';
                '</a>' +
            '</li>';
                
            box.dropbox.append(html);
        }
    };
    
    moveUp = function() {
        if (box.dropbox.find('li.autofill-item').length) {
            if (box.current == null || !box.current.prev().hasClass('autofill-item')) {
                setCurrent(box.dropbox.find('li.autofill-item').last());
            } else if (box.current.prev().hasClass('autofill-item')) {
                setCurrent(box.current.prev());
            }
        }
    };
    
    moveDown = function() {
        if (box.dropbox.find('li.autofill-item').length) {
            if (box.current == null || !box.current.next().hasClass('autofill-item')) {
                setCurrent(box.dropbox.find('li.autofill-item').first());
            } else if (box.current.next().hasClass('autofill-item')) {
                setCurrent(box.current.next());
            }
        }
    };
    
    // jax load dropbox content
    loadDropbox = function() {
        maskDropbox();
        
        if(box.xhr != null && box.xhr.readyState != 4){
            box.xhr.abort();
        }
        box.xhr = $.ajax({
            method: 'GET',
            url: box.url,
            data: {
                keyword: box.value()
            }
        })
        .done(function(data) {
            // box.dropbox.html(html);            
            renderDropboxList(data);
            
            resetCurrent();
            
            // Click to dropbox
            box.find('.autofill-item').on('click', function() {
                setCurrent($(this));
                selectCurrent();
            });
            
            unmaskDropbox();
        });
    };
    
    // ==== END FUNCTIONS =============================
    
    // ---- BEGIN EVENTS ------------------------------
    // On focus
    box.input.on('focus', function() {
        showDropbox();
        loadDropbox();
    });
    
    // Out focus
    box.input.on('focusout', function() {
        setTimeout(function() {hideDropbox();}, 200);
        updateErrorMessage();
    });
    
    // Key up
    box.input.on('keyup', function(event) {        
        if(event.keyCode !== 13 && event.keyCode !== 38 && event.keyCode !== 40 ) {
            toggleDropbox();
            loadDropbox();
        }
        if(event.keyCode == 38) {
            moveUp();
        }
        if(event.keyCode == 40) {
            moveDown();
        }
    });
    
    // Key down
    box.input.on('keydown', function(event) {        
        if(event.keyCode == 13) {
            event.preventDefault();
            
            selectCurrent();
            
            return false;
        }
    });
    
    // ==== END EVENTS =============================

    return box;
};