var mkMenuEditor;
(function (b) {
    var a = mkMenuEditor = {
        options: {
            menuItemDepthPerLevel: 30,
            globalMaxDepth: 1
        },
        menuList: undefined,
        targetList: undefined,
        menusChanged: false,
        isRTL: !! ("undefined" != typeof isRtl && isRtl),
        negateIfRTL: ("undefined" != typeof isRtl && isRtl) ? -1 : 1,
        init: function () {
            a.menuList = b("#menu-to-edit");
            a.targetList = a.menuList;
            this.jQueryExtensions();
            if (a.menuList.length) {
                this.initSortables()
            }
            //this.initToggles();
            //this.initTabManager()
        },
        jQueryExtensions: function () {
            b.fn.extend({
                menuItemDepth: function () {
                    var c = a.isRTL ? this.eq(0).css("margin-right") : this.eq(0).css("margin-left");
                    return a.pxToDepth(c && -1 != c.indexOf("px") ? c.slice(0, -2) : 0)
                },
                updateDepthClass: function (d, c) {
                    return this.each(function () {
                        var e = b(this);
                        c = c || e.menuItemDepth();
                        b(this).removeClass("menu-item-depth-" + c).addClass("menu-item-depth-" + d)
                    })
                },
                shiftDepthClass: function (c) {
                    return this.each(function () {
                        var d = b(this),
                            e = d.menuItemDepth();
                        b(this).removeClass("menu-item-depth-" + e).addClass("menu-item-depth-" + (e + c))
                    })
                },
                childMenuItems: function () {
                    var c = b();
                    this.each(function () {
                        var d = b(this),
                            f = d.menuItemDepth(),
                            e = d.next();
                        while (e.length && e.menuItemDepth() > f) {
                            c = c.add(e);
                            e = e.next()
                        }
                    });
                    return c
                },
                updateParentMenuItemDBId: function () {
                    return this.each(function () {
                        var e = b(this),
                            c = e.find(".menu-item-data-parent-id"),
                            f = e.menuItemDepth(),
                            d = e.prev();
                        if (f == 0) {
                            c.val(0)
                        } else {
                            while (!d[0] || !d[0].className || -1 == d[0].className.indexOf("menu-item") || (d.menuItemDepth() != f - 1)) {
                                d = d.prev()
                            }
                            c.val(d.find(".menu-item-data-db-id").val())
                        }
                    })
                },
                hideAdvancedMenuItemFields: function () {
                    return this.each(function () {
                        var c = b(this);
                        b(".hide-column-tog").not(":checked").each(function () {
                            c.find(".field-" + b(this).val()).addClass("hidden-field")
                        })
                    })
                },
                addSelectedToMenu: function (c) {
                    if (0 == b("#menu-to-edit").length) {
                        return false
                    }
                    return this.each(function () {
                        var e = b(this),
                            d = {},
                            g = e.find(".tabs-panel-active .categorychecklist li input:checked"),
                            f = new RegExp("menu-item\\[([^\\]]*)");
                        c = c || a.addMenuItemToBottom;
                        if (!g.length) {
                            return false
                        }
                        e.find("img.waiting").show();
                        b(g).each(function () {
                            var i = b(this),
                                h = f.exec(i.attr("name")),
                                j = "undefined" == typeof h[1] ? 0 : parseInt(h[1], 10);
                            if (this.className && -1 != this.className.indexOf("add-to-top")) {
                                c = a.addMenuItemToTop
                            }
                            d[j] = i.closest("li").getItemData("add-menu-item", j)
                        });
                        a.addItemToMenu(d, c, function () {
                            g.removeAttr("checked");
                            e.find("img.waiting").hide()
                        })
                    })
                },
                getItemData: function (f, g) {
                    f = f || "menu-item";
                    var d = {},
                        e, c = ["menu-item-db-id", "menu-item-object-id", "menu-item-object", "menu-item-parent-id", "menu-item-position", "menu-item-type", "menu-item-title", "menu-item-url", "menu-item-description", "menu-item-attr-title", "menu-item-target", "menu-item-classes", "menu-item-xfn"];
                    if (!g && f == "menu-item") {
                        g = this.find(".menu-item-data-db-id").val()
                    }
                    if (!g) {
                        return d
                    }
                    this.find("input").each(function () {
                        var h;
                        e = c.length;
                        while (e--) {
                            if (f == "menu-item") {
                                h = c[e] + "[" + g + "]"
                            } else {
                                if (f == "add-menu-item") {
                                    h = "menu-item[" + g + "][" + c[e] + "]"
                                }
                            }
                            if (this.name && h == this.name) {
                                d[c[e]] = this.value
                            }
                        }
                    });
                    return d
                },
                setItemData: function (c, d, e) {
                    d = d || "menu-item";
                    if (!e && d == "menu-item") {
                        e = b(".menu-item-data-db-id", this).val()
                    }
                    if (!e) {
                        return this
                    }
                    this.find("input").each(function () {
                        var f = b(this),
                            g;
                        b.each(c, function (h, i) {
                            if (d == "menu-item") {
                                g = h + "[" + e + "]"
                            } else {
                                if (d == "add-menu-item") {
                                    g = "menu-item[" + e + "][" + h + "]"
                                }
                            }
                            if (g == f.attr("name")) {
                                f.val(i)
                            }
                        })
                    });
                    return this
                }
            })
        },
        initSortables: function () {
            var p = 0,
                e, t, d, l, o, f, c, i, s, m = a.menuList.offset().left,
                h = b("body"),
                q, n = r();
            m += a.isRTL ? a.menuList.width() : 0;
            a.menuList.sortable({
                handle: ".menu-item-handle",
                placeholder: "sortable-placeholder",
                start: function (A, z) {
                    var u, x, w, v, y;
                    if (a.isRTL) {
                        z.item[0].style.right = "auto"
                    }
                    s = z.item.children(".menu-item-transport");
                    e = z.item.menuItemDepth();
                    j(z, e);
                    w = (z.item.next()[0] == z.placeholder[0]) ? z.item.next() : z.item;
                    v = w.childMenuItems();
                    s.append(v);
                    u = s.outerHeight();
                    u += (u > 0) ? (z.placeholder.css("margin-top").slice(0, -2) * 1) : 0;
                    u += z.helper.outerHeight();
                    i = u;
                    u -= 2;
                    z.placeholder.height(u);
                    q = e;
                    v.each(function () {
                        var B = b(this).menuItemDepth();
                        q = (B > q) ? B : q
                    });
                    x = z.helper.find(".menu-item-handle").outerWidth();
                    x += a.depthToPx(q - e);
                    x -= 2;
                    z.placeholder.width(x);
                    y = z.placeholder.next();
                    y.css("margin-top", i + "px");
                    z.placeholder.detach();
                    b(this).sortable("refresh");
                    z.item.after(z.placeholder);
                    y.css("margin-top", 0);
                    k(z)
                },
                stop: function (x, w) {
                    var v, u = p - e;
                    v = s.children().insertAfter(w.item);
                    if (u != 0) {
                        w.item.updateDepthClass(p);
                        v.shiftDepthClass(u);
                        g(u)
                    }
                    a.registerChange();
                    w.item.updateParentMenuItemDBId();
                    w.item[0].style.top = 0;
                    if (a.isRTL) {
                        w.item[0].style.left = "auto";
                        w.item[0].style.right = 0
                    }
                    //a.refreshMenuTabs(true)
                },
                change: function (v, u) {
                    if (!u.placeholder.parent().hasClass("menu")) {
                        (l.length) ? l.after(u.placeholder) : a.menuList.prepend(u.placeholder)
                    }
                    k(u)
                },
                sort: function (w, v) {
                    var y = v.helper.offset(),
                        u = a.isRTL ? y.left + v.helper.width() : y.left,
                        x = a.negateIfRTL * a.pxToDepth(u - m);
                    if (x > d || y.top < f) {
                        x = d
                    } else {
                        if (x < t) {
                            x = t
                        }
                    }
                    if (x != p) {
                        j(v, x)
                    }
                    if (c && y.top + i > c) {
                        o.after(v.placeholder);
                        k(v);
                        b(this).sortable("refreshPositions")
                    }
                }
            });

            function k(u) {
                var v;
                l = u.placeholder.prev();
                o = u.placeholder.next();
                if (l[0] == u.item[0]) {
                    l = l.prev()
                }
                if (o[0] == u.item[0]) {
                    o = o.next()
                }
                f = (l.length) ? l.offset().top + l.height() : 0;
                c = (o.length) ? o.offset().top + o.height() / 3 : 0;
                t = (o.length) ? o.menuItemDepth() : 0;
                if (l.length) {
                    d = ((v = l.menuItemDepth() + 1) > a.options.globalMaxDepth) ? a.options.globalMaxDepth : v
                } else {
                    d = 0
                }
            }
            function j(u, v) {
                u.placeholder.updateDepthClass(v, p);
                p = v
            }
            function r() {
                if (!h[0].className) {
                    return 0
                }
                var u = h[0].className.match(/menu-max-depth-(\d+)/);
                return u && u[1] ? parseInt(u[1]) : 0
            }
            function g(u) {
                var v, w = n;
                if (u === 0) {
                    return
                } else {
                    if (u > 0) {
                        v = q + u;
                        if (v > n) {
                            w = v
                        }
                    } else {
                        if (u < 0 && q == n) {
                            while (!b(".menu-item-depth-" + w, a.menuList).length && w > 0) {
                                w--
                            }
                        }
                    }
                }
                h.removeClass("menu-max-depth-" + n).addClass("menu-max-depth-" + w);
                n = w
            }
        },
        setupInputWithDefaultTitle: function () {
            var c = "input-with-default-title";
            b("." + c).each(function () {
                var f = b(this),
                    e = f.attr("title"),
                    d = f.val();
                f.data(c, e);
                if ("" == d) {
                    f.val(e)
                } else {
                    if (e == d) {
                        return
                    } else {
                        f.removeClass(c)
                    }
                }
            }).focus(function () {
                    var d = b(this);
                    if (d.val() == d.data(c)) {
                        d.val("").removeClass(c)
                    }
                }).blur(function () {
                    var d = b(this);
                    if ("" == d.val()) {
                        d.addClass(c).val(d.data(c))
                    }
                })
        },
        registerChange: function () {
            a.menusChanged = true
        },
        depthToPx: function (c) {
            return c * a.options.menuItemDepthPerLevel
        },
        pxToDepth: function (c) {
            return Math.floor(c / a.options.menuItemDepthPerLevel)
        },
        saveMenu: function(idMenu,event) {
            var menuResult = [];
            var prevBtnHtml = jQuery(event.target).html();
            jQuery(event.target).html('Saving...');

            jQuery('.settings').each(function(){
                var id = jQuery(this).find('.menu-item-data-db-id').val();
                var idParent = jQuery(this).find('.menu-item-data-parent-id').val();
                menuResult.push({'id':id, 'idParent':idParent});
            });
            jQuery.post('../admin/conf_menu.php', {'ids' : menuResult, 'action' : 'saveMenuLevels'}, function(){
                jQuery(event.target).html('Done');
                setTimeout(function(){
                    jQuery(event.target).html(prevBtnHtml);
                }, 2000);
            });
            //console.log(menuResult);
        },
        removeElement: function(idPage, event) {
            jQuery.post('../admin/conf_menu.php', {'idPage' : idPage, 'action' : 'removePage'}, function(){
                jQuery('#menu-item-'+idPage).slideUp().remove();
            });
        }
    };
    b(document).ready(function () {
        mkMenuEditor.init()
    })
})(jQuery);

