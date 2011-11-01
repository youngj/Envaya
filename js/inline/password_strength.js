
/*
 * JS implementation of engine/PasswordStrength.php
 */
var PasswordStrength = {
    ExtremelyWeak: 1,
    VeryWeak: 2,
    Weak: 3,
    BelowAverage: 4,
    Average: 5,
    AboveAverage: 6,
    Strong: 7,
    VeryStrong: 8,
    ExtremelyStrong: 9,

    // from scripts/export_bigrams.php
    bigrams: {"01":2,"02":4,"03":8,"04":8,"05":8,"06":8,"07":8,"08":4,"09":2,"10":2,"12":2,"13":4,"14":4,"15":8,"16":8,"17":8,"18":8,"19":4,"20":4,"21":2,"23":2,"24":4,"25":4,"26":8,"27":8,"28":8,"29":8,"30":8,"31":4,"32":2,"34":2,"35":4,"36":4,"38":8,"39":8,"40":8,"41":4,"42":4,"43":2,"45":2,"46":4,"47":4,"48":8,"49":8,"50":8,"51":8,"52":4,"53":4,"54":2,"56":2,"57":4,"58":4,"59":8,"60":8,"61":8,"62":8,"63":4,"64":4,"65":2,"67":2,"68":4,"69":4,"70":8,"71":8,"72":8,"73":8,"74":4,"75":4,"76":2,"78":2,"79":4,"80":4,"81":8,"82":8,"83":8,"84":8,"85":4,"86":4,"87":2,"89":2,"90":2,"91":4,"92":8,"93":8,"94":8,"95":8,"96":4,"97":4,"98":2,"ab":2,"ac":3,"ad":3,"ae":7,"af":5,"ag":4,"ah":7,"ai":3,"aj":9,"ak":5,"al":2,"am":4,"an":1,"ao":8,"ap":4,"aq":4,"ar":2,"as":2,"at":2,"au":5,"av":3,"aw":5,"ax":8,"ay":4,"az":6,"ba":2,"bc":2,"bd":9,"be":2,"bi":5,"bj":7,"bl":4,"bm":9,"bn":4,"bo":4,"br":4,"bs":6,"bt":7,"bu":4,"bv":4,"by":4,"ca":3,"cb":2,"cd":2,"ce":3,"ch":3,"ci":4,"ck":4,"cl":4,"co":3,"cq":8,"cr":5,"cs":9,"ct":3,"cu":5,"cv":4,"cx":4,"cy":7,"da":4,"db":9,"dc":2,"de":2,"df":4,"dg":6,"dh":9,"di":3,"dl":6,"dm":7,"dn":7,"do":4,"dr":5,"ds":4,"dt":9,"du":5,"dv":7,"dw":8,"dy":6,"ea":2,"eb":7,"ec":3,"ed":2,"ef":2,"eg":5,"eh":6,"ei":4,"ej":9,"ek":7,"el":3,"em":3,"en":2,"eo":6,"ep":4,"eq":6,"er":1,"es":2,"et":3,"eu":7,"ev":4,"ew":4,"ex":4,"ey":4,"ez":9,"fa":4,"fd":4,"fe":2,"fg":2,"fi":4,"fl":5,"fo":3,"fr":4,"fs":7,"ft":5,"fu":5,"fy":9,"ga":4,"gd":9,"ge":3,"gf":2,"gh":2,"gi":5,"gl":5,"gm":8,"gn":6,"go":4,"gr":4,"gs":5,"gt":7,"gu":5,"gy":8,"ha":2,"hb":8,"hc":7,"hd":9,"he":1,"hf":9,"hg":2,"hi":2,"hj":4,"hl":8,"hm":8,"hn":8,"ho":3,"hq":9,"hr":5,"hs":7,"ht":4,"hu":5,"hw":8,"hy":6,"ia":4,"ib":5,"ic":3,"id":3,"ie":3,"if":4,"ig":4,"ih":2,"ij":2,"ik":5,"il":3,"im":3,"in":1,"io":3,"ip":6,"iq":9,"ir":3,"is":2,"it":2,"iu":4,"iv":4,"ix":7,"iz":6,"ja":6,"je":6,"jh":4,"ji":2,"jk":2,"jo":6,"ju":6,"ka":7,"ke":4,"kf":9,"ki":5,"kj":2,"kl":2,"kn":5,"ko":8,"ks":6,"ku":9,"kw":9,"ky":8,"la":3,"lb":9,"lc":8,"ld":3,"le":2,"lf":5,"lg":8,"li":3,"lk":2,"lm":2,"ln":9,"lo":3,"lp":7,"lr":8,"ls":5,"lt":5,"lu":5,"lv":7,"lw":7,"ly":3,"ma":3,"mb":5,"md":8,"me":2,"mf":8,"mi":4,"ml":2,"mn":2,"mo":3,"mp":4,"mr":6,"ms":5,"mt":9,"mu":5,"my":4,"na":4,"nb":4,"nc":3,"nd":1,"ne":2,"nf":6,"ng":2,"nh":7,"ni":4,"nj":8,"nk":5,"nl":5,"nm":2,"no":2,"np":9,"nq":8,"nr":9,"ns":3,"nt":2,"nu":5,"nv":6,"nw":8,"nx":9,"ny":5,"oa":5,"ob":5,"oc":5,"od":4,"oe":6,"of":2,"og":6,"oh":7,"oi":4,"ok":5,"ol":4,"om":3,"on":2,"op":2,"or":2,"os":3,"ot":3,"ou":2,"ov":4,"ow":3,"ox":9,"oy":6,"oz":8,"pa":4,"pe":3,"ph":6,"pi":5,"pl":4,"po":2,"pq":2,"pr":3,"ps":6,"pt":5,"pu":5,"pw":9,"py":8,"qa":6,"qp":2,"qr":2,"qu":4,"qw":4,"ra":3,"rb":7,"rc":5,"rd":4,"re":1,"rf":6,"rg":5,"rh":7,"ri":3,"rk":5,"rl":5,"rm":4,"rn":4,"ro":2,"rp":6,"rq":2,"rs":2,"rt":3,"ru":4,"rv":6,"rw":7,"ry":4,"rz":6,"sa":4,"sb":9,"sc":5,"sd":4,"se":2,"sf":8,"sg":9,"sh":3,"si":3,"sk":6,"sl":5,"sm":6,"sn":7,"so":3,"sp":4,"sq":8,"sr":2,"st":2,"su":4,"sw":6,"sx":6,"sy":7,"ta":3,"tc":6,"te":2,"tf":8,"th":1,"ti":2,"tl":4,"tm":8,"tn":7,"to":2,"tr":3,"ts":2,"tu":2,"tw":5,"ty":4,"tz":9,"ua":5,"ub":5,"uc":4,"ud":5,"ue":5,"uf":7,"ug":4,"ui":4,"ul":3,"um":5,"un":3,"uo":8,"up":4,"ur":3,"us":3,"ut":2,"uv":2,"ux":9,"uy":4,"uz":9,"va":4,"vb":4,"vc":4,"vd":8,"ve":2,"vi":4,"vn":7,"vo":6,"vu":2,"vw":2,"vy":9,"wa":3,"wd":9,"we":3,"wf":9,"wh":3,"wi":3,"wl":7,"wn":5,"wo":4,"wq":4,"wr":7,"ws":6,"wt":9,"wu":9,"wv":2,"wx":2,"xa":7,"xc":4,"xe":7,"xh":9,"xi":7,"xp":6,"xs":6,"xt":6,"xu":9,"xw":2,"xy":2,"xz":4,"ya":7,"yb":8,"yc":9,"yd":9,"ye":5,"yf":9,"yi":6,"yl":8,"ym":8,"yn":9,"yo":4,"yp":8,"yr":8,"ys":5,"yt":4,"yu":4,"yw":9,"yx":2,"yz":2,"za":6,"ze":6,"zi":8,"zl":9,"zo":8,"zx":4,"zy":2,"!@":2,"@!":2,"~!":2,"!~":2,"@#":2,"#@":2,"#$":2,"$#":2,"$%":2,"%$":2,"%^":2,"^%":2,"&^":2,"^&":2,"&*":2,"*&":2,"*(":2,"(*":2,"()":2,")(":2,")_":2,"_)":2,"-=":2,"=-":2,"_+":2,"+_":2,",.":2,".,":2,".\/":2,"\/.":2,"<>":2,"><":2,"?>":2,">?":2,";'":2,"';":2,":\"":2,"\":":2,"m,":2,",m":2,"l;":2,";l":2,"[]":2,"][":2,"{}":2,"}{":2,"p[":2,"[p":2,"0-":2,"-0":2},

    calculate: function(password, easyWords)
    {
        var P = PasswordStrength;
        var len = password.length;

        if (len < 2)
        {
            return P.ExtremelyWeak;
        }
    
        var lpass = password.toLowerCase();
        if (easyWords)
        {
            for (var i = 0; i < easyWords.length; i++)
            {               
                var easy = easyWords[i];
                if (!easy)
                {   
                    continue;
                }
                var leasy = easy.toLowerCase();
                        
                if (leasy.indexOf(lpass) != -1 || lpass.indexOf(leasy) != -1)
                {   
                    return P.ExtremelyWeak;
                }
            }
        }

        var hasLowercase = password.match(/[a-z]/);
        var hasUppercase = password.match(/[A-Z]/);
        var hasNumber = password.match(/[0-9]/);
        var hasOther = password.match(/[^a-zA-Z0-9]/);

        var alphabetSize = 0;
        if (hasLowercase)
        {
            alphabetSize += 26;
        }        
        if (hasUppercase)
        {
            alphabetSize += 26;
        }        
        if (hasNumber)
        {
            alphabetSize += 10;
        }
        if (hasOther)
        {
            alphabetSize += 32;
        }
        if (!alphabetSize)
        {
            alphabetSize = 1;
        }
                
        var bigrams = P.bigrams;
        
        var bscore = 1;        
        for (var i = 0; i < len - 1; i++)
        {
            var bigram = password.substr(i, 2);
            
            bscore++;
            
            if (bigrams[bigram])
            {
                bscore += bigrams[bigram];
            }
            else if (bigram[0] === bigram[1])
            {
                bscore++;
            }
            else
            {
                bscore += 10;
            }
        }
        
        score = Math.round(Math.log(alphabetSize) * bscore / (50 * Math.log(2)));    
        score = Math.max(score, P.ExtremelyWeak);
        score = Math.min(score, P.ExtremelyStrong);
        return score;
    },
    
    show: function(password, easyWords, minStrength, div)
    {        
        var strength = PasswordStrength.calculate(password, easyWords);               
        var colors = {
            1: '#f00', 2: '#f30', 3: '#f60',
            4: '#f90', 5: '#ee0', 6: '#ce0',
            7: '#ae0', 8: '#6e0', 9: '#3e0'
        };                       
        var color = (strength < minStrength) ? colors[1] : colors[strength];
        div.style.backgroundColor = color;
        div.style.width = (strength * 20) + 'px';            
    }    
};
