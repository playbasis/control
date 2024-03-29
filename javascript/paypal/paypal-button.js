if ("undefined" == typeof PAYPAL || !PAYPAL) var PAYPAL = {};

PAYPAL.apps = PAYPAL.apps || {}, function(a) {
    "use strict";
    function b() {
        var b, c, d, e;
        a.getElementById("paypal-button") || (b = "", c = a.createElement("style"), d = ".paypal-button",
            e = d + " button", b += d + " { white-space: nowrap; }", b += d + " .field-error {  border: 1px solid #FF0000; }",
            b += d + " .hide { display: none; }", b += d + " .error-box { background: #FFFFFF; border: 1px solid #DADADA; border-radius: 5px; padding: 8px; display: inline-block; }",
            b += e + ' { white-space: nowrap; overflow: hidden; border-radius: 13px; font-family: "Arial", bold, italic; font-weight: bold; font-style: italic; border: 1px solid #ffa823; color: #0E3168; background: #ffa823; position: relative; text-shadow: 0 1px 0 rgba(255,255,255,.5); cursor: pointer; z-index: 0; }',
            b += e + ':before { content: " "; position: absolute; width: 100%; height: 100%; border-radius: 11px; top: 0; left: 0; background: #ffa823; background: -webkit-linear-gradient(top, #FFAA00 0%,#FFAA00 80%,#FFF8FC 100%); background: -moz-linear-gradient(top, #FFAA00 0%,#FFAA00 80%,#FFF8FC 100%); background: -ms-linear-gradient(top, #FFAA00 0%,#FFAA00 80%,#FFF8FC 100%); background: linear-gradient(top, #FFAA00 0%,#FFAA00 80%,#FFF8FC 100%); z-index: -2; }',
            b += e + ':after { content: " "; position: absolute; width: 98%; height: 60%; border-radius: 40px 40px 38px 38px; top: 0; left: 0; background: -webkit-linear-gradient(top, #fefefe 0%, #fed994 100%); background: -moz-linear-gradient(top, #fefefe 0%, #fed994 100%); background: -ms-linear-gradient(top, #fefefe 0%, #fed994 100%); background: linear-gradient(top, #fefefe 0%, #fed994 100%); z-index: -1; -webkit-transform: translateX(1%);-moz-transform: translateX(1%); -ms-transform: translateX(1%); transform: translateX(1%); }',
            b += e + ".small { padding: 3px 15px; font-size: 12px; }", b += e + ".large { padding: 4px 19px; font-size: 14px; }",
            c.type = "text/css", c.id = "paypal-button", c.styleSheet ? c.styleSheet.cssText = b : c.appendChild(a.createTextNode(b)),
            a.getElementsByTagName("head")[0].appendChild(c));
    }
    function c(b, c) {
        var e, f, g, h, k, l, m, o, p, q, r, s, t, u, v, w = a.createElement("form"), x = a.createElement("button"), y = a.createElement("input"), z = a.createElement("p"), A = a.createElement("label"), B = a.createElement("input"), C = a.createElement("select"), D = a.createElement("option"), E = b.items, F = [], G = {};
        w.method = "post", w.action = j.replace("{env}", b.items.env.value), w.className = "paypal-button",
            w.target = "_top";
        var H = a.createElement("div");
        H.className = "hide", H.id = "errorBox", w.appendChild(H), B.type = "text", B.className = "paypal-input",
            z.className = "paypal-group", A.className = "paypal-label", C.className = "paypal-select",
            y.type = "hidden", l = E.size && E.size.value || "large", m = E.lc && E.lc.value || "en_US",
            o = n[m] || n.en_US, p = o[c], b.items.text && (p = b.items.text.value, b.remove("text"));
        for (k in E) e = E[k], e.hasOptions ? F.push(e) : e.isEditable ? (h = B.cloneNode(!0),
            h.name = e.key, h.value = e.value, g = A.cloneNode(!0), v = i.config.labels[e.key] || o[e.key] || e.key,
            g.htmlFor = e.key, g.appendChild(a.createTextNode(v)), g.appendChild(h), f = z.cloneNode(!0),
            f.appendChild(g), w.appendChild(f)) : (h = f = y.cloneNode(!0), h.name = e.key,
            h.value = e.value, w.appendChild(f));
        F = d(F);
        for (k in F) if (e = F[k], F[k].hasOptions) {
            if (G = e.value, G.options.length > 1) {
                h = y.cloneNode(!0), h.name = "on" + e.displayOrder, h.value = G.label, q = C.cloneNode(!0),
                    q.name = "os" + e.displayOrder;
                for (s in G.options) if (t = G.options[s], "string" == typeof t) r = D.cloneNode(!0),
                    r.value = t, r.appendChild(a.createTextNode(t)), q.appendChild(r); else for (u in t) r = D.cloneNode(!0),
                    r.value = u, r.appendChild(a.createTextNode(t[u])), q.appendChild(r);
                g = A.cloneNode(!0), v = G.label || e.key, g.htmlFor = e.key, g.appendChild(a.createTextNode(v)),
                    g.appendChild(q), g.appendChild(h);
            } else g = A.cloneNode(!0), v = G.label || e.key, g.htmlFor = e.key, g.appendChild(a.createTextNode(v)),
                h = y.cloneNode(!0), h.name = "on" + e.displayOrder, h.value = G.label, g.appendChild(h),
                h = B.cloneNode(!0), h.name = "os" + e.displayOrder, h.value = G.options[0] || "",
                h.setAttribute("data-label", G.label), g.appendChild(h);
            f = z.cloneNode(!0), f.appendChild(g), w.appendChild(f);
        }
        try {
            x.type = "submit";
        } catch (I) {
            x.setAttribute("type", "submit");
        }
        return x.className = "paypal-button " + l, x.appendChild(a.createTextNode(p)), w.appendChild(x),
            w;
    }
    function d(a) {
        return a.sort(function(a, b) {
            return a.displayOrder - b.displayOrder;
        }), a;
    }
    function e(b, c) {
        var d, e, f = j.replace("{env}", b.items.env.value), g = a.createElement("img"), h = f + "?", i = 13, l = b.items;
        c = c && c.value || 250;
        for (e in l) d = l[e], h += d.key + "=" + encodeURIComponent(d.value) + "&";
        return h = encodeURIComponent(h), g.src = k.replace("{env}", b.items.env.value).replace("{url}", h).replace("{pattern}", i).replace("{size}", c),
            g;
    }
    function f(a) {
        var b, c, d, e, f, h = {}, i = [];
        if (b = a.attributes) for (f = 0, e = b.length; e > f; f++) c = b[f], (d = c.name.match(/^data-option([0-9])([a-z]+)([0-9])?/i)) ? i.push({
            name: "option." + d[1] + "." + d[2] + (d[3] ? "." + d[3] : ""),
            value: c.value
        }) : (d = c.name.match(/^data-([a-z0-9_]+)(-editable)?/i)) && (h[d[1]] = {
            value: c.value,
            isEditable: !!d[2]
        });
        return g(i, h), h;
    }
    function g(a, b) {
        var c, d, e, f, g, h = {};
        for (j = 0; j < a.length; j++) for (c = a[j], d = c.name, e = d.split("."), f = e.shift(),
                                                g = h; f; ) g[f] || (g[f] = {}), e.length || (g[f] = c.value), g = g[f], f = e.shift();
        var i, j, k, l, m = {}, n = {}, o = [], p = {}, q = Object.prototype.hasOwnProperty;
        for (i in h) if (q.call(h, i)) {
            l = h[i];
            for (j in l) {
                b["option_" + j] = {
                    value: {
                        options: "",
                        label: l[j].name
                    },
                    hasOptions: !0,
                    displayOrder: parseInt(j, 10)
                }, m = l[j].select, n = l[j].price, o = [];
                for (k in m) p = {}, n ? (p[m[k]] = m[k] + " " + n[k], o.push(p)) : o.push(m[k]);
                b["option_" + j].value.options = o;
            }
        }
    }
    function h() {
        this.items = {}, this.add = function(a, b, c, d, e) {
            this.items[a] = {
                key: a,
                value: b,
                isEditable: c,
                hasOptions: d,
                displayOrder: e
            };
        }, this.remove = function(a) {
            delete this.items[a];
        };
    }
    var i = {}, j = "https://{env}.paypal.com/cgi-bin/webscr", k = "https://{env}.paypal.com/webapps/ppint/qrcode?data={url}&pattern={pattern}&height={size}", l = "JavaScriptButton_{type}", m = {
        name: "item_name",
        number: "item_number",
        locale: "lc",
        currency: "currency_code",
        recurrence: "p3",
        period: "t3",
        callback: "notify_url",
        button_id: "hosted_button_id"
    }, n = {
        da_DK: {
            buynow: "Køb nu",
            cart: "Læg i indkøbsvogn",
            donate: "Doner",
            subscribe: "Abonner",
            paynow: "Betal nu",
            item_name: "Vare",
            number: "Nummer",
            amount: "Pris",
            quantity: "Antal"
        },
        de_DE: {
            buynow: "Jetzt kaufen",
            cart: "In den Warenkorb",
            donate: "Spenden",
            subscribe: "Abonnieren",
            paynow: "Jetzt bezahlen",
            item_name: "Artikel",
            number: "Nummer",
            amount: "Betrag",
            quantity: "Menge"
        },
        en_AU: {
            buynow: "Buy Now",
            cart: "Add to Cart",
            donate: "Donate",
            subscribe: "Subscribe",
            paynow: "Pay Now",
            item_name: "Item",
            number: "Number",
            amount: "Amount",
            quantity: "Quantity"
        },
        en_GB: {
            buynow: "Buy Now",
            cart: "Add to Cart",
            donate: "Donate",
            subscribe: "Subscribe",
            paynow: "Pay Now",
            item_name: "Item",
            number: "Number",
            amount: "Amount",
            quantity: "Quantity"
        },
        en_US: {
            buynow: "Buy Now",
            cart: "Add to Cart",
            donate: "Donate",
            subscribe: "Subscribe",
            paynow: "Pay Now",
            item_name: "Item",
            number: "Number",
            amount: "Amount",
            quantity: "Quantity"
        },
        es_ES: {
            buynow: "Comprar ahora",
            cart: "Añadir al carro",
            donate: "Donar",
            subscribe: "Suscribirse",
            paynow: "Pague ahora",
            item_name: "Artículo",
            number: "Número",
            amount: "Importe",
            quantity: "Cantidad"
        },
        es_XC: {
            buynow: "Comprar ahora",
            cart: "Añadir al carrito",
            donate: "Donar",
            subscribe: "Suscribirse",
            paynow: "Pague ahora",
            item_name: "Artículo",
            number: "Número",
            amount: "Importe",
            quantity: "Cantidad"
        },
        fr_CA: {
            buynow: "Acheter",
            cart: "Ajouter au panier",
            donate: "Faire un don",
            subscribe: "Souscrire",
            paynow: "Payer maintenant",
            item_name: "Objet",
            number: "Numéro",
            amount: "Montant",
            quantity: "Quantité"
        },
        fr_FR: {
            buynow: "Acheter",
            cart: "Ajouter au panier",
            donate: "Faire un don",
            subscribe: "Souscrire",
            paynow: "Payer maintenant",
            item_name: "Objet",
            number: "Numéro",
            amount: "Montant",
            quantity: "Quantité"
        },
        fr_XC: {
            buynow: "Acheter",
            cart: "Ajouter au panier",
            donate: "Faire un don",
            subscribe: "Souscrire",
            paynow: "Payer maintenant",
            item_name: "Objet",
            number: "Numéro",
            amount: "Montant",
            quantity: "Quantité"
        },
        he_IL: {
            buynow: "וישכע הנק",
            cart: "תוינקה לסל ףסוה",
            donate: "םורת",
            subscribe: "יונמכ ףרטצה",
            paynow: "כשיו שלם ע",
            item_name: "טירפ",
            number: "רפסמ",
            amount: "םוכס",
            quantity: "מותכ"
        },
        id_ID: {
            buynow: "Beli Sekarang",
            cart: "Tambah ke Keranjang",
            donate: "Donasikan",
            subscribe: "Berlangganan",
            paynow: "Bayar Sekarang",
            item_name: "Barang",
            number: "Nomor",
            amount: "Harga",
            quantity: "Kuantitas"
        },
        it_IT: {
            buynow: "Paga adesso",
            cart: "Aggiungi al carrello",
            donate: "Donazione",
            subscribe: "Iscriviti",
            paynow: "Paga Ora",
            item_name: "Oggetto",
            number: "Numero",
            amount: "Importo",
            quantity: "Quantità"
        },
        ja_JP: {
            buynow: "今すぐ購入",
            cart: "カートに追加",
            donate: "寄付",
            subscribe: "購読",
            paynow: "今すぐ支払う",
            item_name: "商品",
            number: "番号",
            amount: "価格",
            quantity: "数量"
        },
        nl_NL: {
            buynow: "Nu kopen",
            cart: "Aan winkelwagentje toevoegen",
            donate: "Doneren",
            subscribe: "Abonneren",
            paynow: "Nu betalen",
            item_name: "Item",
            number: "Nummer",
            amount: "Bedrag",
            quantity: "Hoeveelheid"
        },
        no_NO: {
            buynow: "Kjøp nå",
            cart: "Legg til i kurv",
            donate: "Doner",
            subscribe: "Abonner",
            paynow: "Betal nå",
            item_name: "Vare",
            number: "Nummer",
            amount: "Beløp",
            quantity: "Antall"
        },
        pl_PL: {
            buynow: "Kup teraz",
            cart: "Dodaj do koszyka",
            donate: "Przekaż darowiznę",
            subscribe: "Subskrybuj",
            paynow: "Zapłać teraz",
            item_name: "Przedmiot",
            number: "Numer",
            amount: "Kwota",
            quantity: "Ilość"
        },
        pt_BR: {
            buynow: "Comprar agora",
            cart: "Adicionar ao carrinho",
            donate: "Doar",
            subscribe: "Assinar",
            paynow: "Pagar agora",
            item_name: "Produto",
            number: "Número",
            amount: "Valor",
            quantity: "Quantidade"
        },
        ru_RU: {
            buynow: "Купить сейчас",
            cart: "Добавить в корзину",
            donate: "Пожертвовать",
            subscribe: "Подписаться",
            paynow: "Оплатить сейчас",
            item_name: "Товар",
            number: "Номер",
            amount: "Сумма",
            quantity: "Количество"
        },
        sv_SE: {
            buynow: "Köp nu",
            cart: "Lägg till i kundvagn",
            donate: "Donera",
            subscribe: "Abonnera",
            paynow: "Betal nu",
            item_name: "Objekt",
            number: "Nummer",
            amount: "Belopp",
            quantity: "Antal"
        },
        th_TH: {
            buynow: "ซื้อทันที",
            cart: "เพิ่มลงตะกร้า",
            donate: "บริจาค",
            subscribe: "บอกรับสมาชิก",
            paynow: "จ่ายตอนนี้",
            item_name: "ชื่อสินค้า",
            number: "รหัสสินค้า",
            amount: "ราคา",
            quantity: "จำนวน"
        },
        tr_TR: {
            buynow: "Hemen Alın",
            cart: "Sepete Ekleyin",
            donate: "Bağış Yapın",
            subscribe: "Abone Olun",
            paynow: "Şimdi öde",
            item_name: "Ürün",
            number: "Numara",
            amount: "Tutar",
            quantity: "Miktar"
        },
        zh_CN: {
            buynow: "立即购买",
            cart: "添加到购物车",
            donate: "捐赠",
            subscribe: "租用",
            paynow: "现在支付",
            item_name: "物品",
            number: "编号",
            amount: "金额",
            quantity: "数量"
        },
        zh_HK: {
            buynow: "立即買",
            cart: "加入購物車",
            donate: "捐款",
            subscribe: "訂用",
            paynow: "现在支付",
            item_name: "項目",
            number: "號碼",
            amount: "金額",
            quantity: "數量"
        },
        zh_TW: {
            buynow: "立即購",
            cart: "加到購物車",
            donate: "捐款",
            subscribe: "訂閱",
            paynow: "现在支付",
            item_name: "商品",
            number: "商品編號",
            amount: "單價",
            quantity: "數量"
        },
        zh_XC: {
            buynow: "立即购买",
            cart: "添加到购物车",
            donate: "捐赠",
            subscribe: "租用",
            paynow: "现在支付",
            item_name: "物品",
            number: "编号",
            amount: "金额",
            quantity: "数量"
        }
    };
    if (PAYPAL.apps.ButtonFactory || (i.config = {
        labels: {}
    }, i.buttons = {
        buynow: 0,
        cart: 0,
        donate: 0,
        qr: 0,
        subscribe: 0
    }, i.create = function(a, d, f, g) {
        var i, j, k, n, o = new h();
        if (!a) return !1;
        for (j in d) n = d[j], o.add(m[j] || j, n.value, n.isEditable, n.hasOptions, n.displayOrder);
        return f = f || "buynow", k = "www", o.items.env && o.items.env.value && (k += "." + o.items.env.value),
            o.items.hosted_button_id ? o.add("cmd", "_s-xclick") : "cart" === f ? (o.add("cmd", "_cart"),
                o.add("add", !0)) : "donate" === f ? o.add("cmd", "_donations") : "subscribe" === f ? (o.add("cmd", "_xclick-subscriptions"),
                o.items.amount && !o.items.a3 && o.add("a3", o.items.amount.value)) : o.add("cmd", "_xclick"),
            o.add("business", a), o.add("bn", l.replace(/\{type\}/, f)), o.add("env", k), "qr" === f ? (i = e(o, o.items.size),
            o.remove("size")) : i = c(o, f), b(), this.buttons[f] += 1, g && g.appendChild(i),
            i;
    }, PAYPAL.apps.ButtonFactory = i), "undefined" != typeof a) {
        var o, p, q, r, s, t, u = PAYPAL.apps.ButtonFactory, v = a.getElementsByTagName("script");
        for (s = 0, t = v.length; t > s; s++) o = v[s], o && o.src && (p = o && f(o), q = p && p.button && p.button.value,
            r = o.src.split("?merchant=")[1], r && (u.create(r, p, q, o.parentNode), o.parentNode.removeChild(o)));
    }
}(document), "object" == typeof module && "object" == typeof module.exports && (module.exports = PAYPAL);