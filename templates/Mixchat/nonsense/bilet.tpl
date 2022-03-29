<script type="text/javascript">
var nonsenseNumSelNum = 0;
var nonsensePrevSelNum = 0;
</script>
<div class="miniature_box">
 <div class="miniature_pos" style="width:400px;padding-right:15px">
  <div class="miniature_title fl_l apps_box_text">Покупка билета</div><a class="cursor_pointer fl_r" style="font-size:12px" onClick="viiBox.clos('nonsenseBilte', 1)">Закрыть</a>
  <div class="clear"></div>
   <div class="rating_text" style="margin-bottom:13px">Выберите 6 чисел из 45.</div>
   <div class="err_red no_display" id="nonsenseNoBalance" style="font-weight:normal;margin-top:-5px;border:1px solid #DF9B9B">У Вас <b>недостаточно</b> mix. <a href="/balance" onClick="Page.Go(this.href); return false">Пополнить баланс</a></div>
   <div class="err_red no_display" id="nonsenseNoSel" style="font-weight:normal;margin-top:-5px;border:1px solid #DF9B9B"></div>
   <div class="nonsense_bilet_number" id="sel_1" onClick="nonsense.selNum(this.innerHTML)">1</div>
   <div class="nonsense_bilet_number" id="sel_2" onClick="nonsense.selNum(this.innerHTML)">2</div>
   <div class="nonsense_bilet_number" id="sel_3" onClick="nonsense.selNum(this.innerHTML)">3</div>
   <div class="nonsense_bilet_number" id="sel_4" onClick="nonsense.selNum(this.innerHTML)">4</div>
   <div class="nonsense_bilet_number" id="sel_5" onClick="nonsense.selNum(this.innerHTML)">5</div>
   <div class="nonsense_bilet_number" id="sel_6" onClick="nonsense.selNum(this.innerHTML)">6</div>
   <div class="nonsense_bilet_number" id="sel_7" onClick="nonsense.selNum(this.innerHTML)">7</div>
   <div class="nonsense_bilet_number" id="sel_8" onClick="nonsense.selNum(this.innerHTML)">8</div>
   <div class="nonsense_bilet_number" id="sel_9" onClick="nonsense.selNum(this.innerHTML)">9</div>
   <div class="nonsense_bilet_number" id="sel_10" onClick="nonsense.selNum(this.innerHTML)">10</div>
   <div class="nonsense_bilet_number" id="sel_11" onClick="nonsense.selNum(this.innerHTML)">11</div>
   <div class="nonsense_bilet_number" id="sel_12" onClick="nonsense.selNum(this.innerHTML)">12</div>
   <div class="nonsense_bilet_number" id="sel_13" onClick="nonsense.selNum(this.innerHTML)">13</div>
   <div class="nonsense_bilet_number" id="sel_14" onClick="nonsense.selNum(this.innerHTML)">14</div>
   <div class="nonsense_bilet_number" id="sel_15" onClick="nonsense.selNum(this.innerHTML)">15</div>
   <div class="nonsense_bilet_number" id="sel_16" onClick="nonsense.selNum(this.innerHTML)">16</div>
   <div class="nonsense_bilet_number" id="sel_17" onClick="nonsense.selNum(this.innerHTML)">17</div>
   <div class="nonsense_bilet_number" id="sel_18" onClick="nonsense.selNum(this.innerHTML)">18</div>
   <div class="nonsense_bilet_number" id="sel_19" onClick="nonsense.selNum(this.innerHTML)">19</div>
   <div class="nonsense_bilet_number" id="sel_20" onClick="nonsense.selNum(this.innerHTML)">20</div>
   <div class="nonsense_bilet_number" id="sel_21" onClick="nonsense.selNum(this.innerHTML)">21</div>
   <div class="nonsense_bilet_number" id="sel_22" onClick="nonsense.selNum(this.innerHTML)">22</div>
   <div class="nonsense_bilet_number" id="sel_23" onClick="nonsense.selNum(this.innerHTML)">23</div>
   <div class="nonsense_bilet_number" id="sel_24" onClick="nonsense.selNum(this.innerHTML)">24</div>
   <div class="nonsense_bilet_number" id="sel_25" onClick="nonsense.selNum(this.innerHTML)">25</div>
   <div class="nonsense_bilet_number" id="sel_26" onClick="nonsense.selNum(this.innerHTML)">26</div>
   <div class="nonsense_bilet_number" id="sel_27" onClick="nonsense.selNum(this.innerHTML)">27</div>
   <div class="nonsense_bilet_number" id="sel_28" onClick="nonsense.selNum(this.innerHTML)">28</div>
   <div class="nonsense_bilet_number" id="sel_29" onClick="nonsense.selNum(this.innerHTML)">29</div>
   <div class="nonsense_bilet_number" id="sel_30" onClick="nonsense.selNum(this.innerHTML)">30</div>
   <div class="nonsense_bilet_number" id="sel_31" onClick="nonsense.selNum(this.innerHTML)">31</div>
   <div class="nonsense_bilet_number" id="sel_32" onClick="nonsense.selNum(this.innerHTML)">32</div>
   <div class="nonsense_bilet_number" id="sel_33" onClick="nonsense.selNum(this.innerHTML)">33</div>
   <div class="nonsense_bilet_number" id="sel_34" onClick="nonsense.selNum(this.innerHTML)">34</div>
   <div class="nonsense_bilet_number" id="sel_35" onClick="nonsense.selNum(this.innerHTML)">35</div>
   <div class="nonsense_bilet_number" id="sel_36" onClick="nonsense.selNum(this.innerHTML)">36</div>
   <div class="nonsense_bilet_number" id="sel_37" onClick="nonsense.selNum(this.innerHTML)">37</div>
   <div class="nonsense_bilet_number" id="sel_38" onClick="nonsense.selNum(this.innerHTML)">38</div>
   <div class="nonsense_bilet_number" id="sel_39" onClick="nonsense.selNum(this.innerHTML)">39</div>
   <div class="nonsense_bilet_number" id="sel_40" onClick="nonsense.selNum(this.innerHTML)">40</div>
   <div class="nonsense_bilet_number" id="sel_41" onClick="nonsense.selNum(this.innerHTML)">41</div>
   <div class="nonsense_bilet_number" id="sel_42" onClick="nonsense.selNum(this.innerHTML)">42</div>
   <div class="nonsense_bilet_number" id="sel_43" onClick="nonsense.selNum(this.innerHTML)">43</div>
   <div class="nonsense_bilet_number" id="sel_44" onClick="nonsense.selNum(this.innerHTML)">44</div>
   <div class="nonsense_bilet_number" id="sel_45" onClick="nonsense.selNum(this.innerHTML)">45</div>
   <input type="hidden" id="selected_numbers" />
   <div class="button_div fl_l no_display" id="nonsesneDivBuyt" style="margin-left:138px;margin-top:13px"><button onClick="nonsense.buy()" id="nonsenseBuyBut">Все верно, купить</button></div>
  <div class="clear"></div>
 </div>
 <div class="clear" style="height:50px"></div>
</div>