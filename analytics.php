<div class="space-y-4">
  <div class="section-header"><h2>Analytics Dashboard</h2>
    <span class="text-[12px] text-slate-500">Live data from Supabase</span>
  </div>
  <div class="grid-4" style="gap:12px">
    <?php
    $kpis=[['Revenue Growth','+12.4%','#16A34A'],['Customer Retention','87%','#2563EB'],
           ['Inventory Turnover','4.2x','#7C3AED'],['Gross Margin','45.6%','#F59E0B']];
    foreach($kpis as [$l,$v,$c]):?>
    <div class="kpi"><div class="flex items-center justify-between mb-2"><span class="text-[11.5px] font-medium text-slate-500"><?=$l?></span></div>
      <span class="text-[20px] font-bold" style="color:<?=$c?>"><?=$v?></span></div>
    <?php endforeach;?>
  </div>
  <div class="grid-2" style="gap:16px">
    <div class="card"><h3 class="text-[14px] font-bold text-[#111827] mb-3">Revenue Trend</h3><canvas id="an-rev" height="180"></canvas></div>
    <div class="card"><h3 class="text-[14px] font-bold text-[#111827] mb-3">Sales by Category</h3><canvas id="an-cat" height="180"></canvas></div>
  </div>
  <div class="grid-3" style="gap:16px">
    <div class="card"><h3 class="text-[14px] font-bold text-[#111827] mb-3">Top Customers</h3>
      <?php $tops=[['Mwangi Supplies',8500000],['Karibu Supermarket',5800000],['Dar Tech',4200000],['Salama Retailers',3100000]];
      foreach($tops as [$n,$v]):?>
      <div class="stat"><span class="text-[12.5px] font-medium text-[#111827]"><?=$n?></span>
        <span class="text-[12.5px] font-bold text-[#16A34A]"><?=CURRENCY?> <?=number_format($v)?></span></div>
      <?php endforeach;?>
    </div>
    <div class="card"><h3 class="text-[14px] font-bold text-[#111827] mb-3">Performance Score</h3>
      <?php $metrics=[['Sales',92],['Operations',78],['Finance',88],['HR',71],['Customer Satisfaction',85]];
      foreach($metrics as [$m,$s]):?>
      <div class="stat">
        <span class="text-[12px] font-medium text-slate-600" style="width:140px"><?=$m?></span>
        <div style="flex:1;height:6px;background:#F1F5F9;border-radius:3px;margin:0 8px">
          <div style="width:<?=$s?>%;height:100%;background:<?=$s>80?'#16A34A':$s>60?'#F59E0B':'#EF4444'?>;border-radius:3px"></div></div>
        <span class="text-[11px] font-bold text-[#111827]"><?=$s?>%</span></div>
      <?php endforeach;?>
    </div>
    <div class="card"><h3 class="text-[14px] font-bold text-[#111827] mb-3">Monthly Targets</h3>
      <?php $targets=[['Revenue','TZS 34.8M','TZS 30M',true],['Orders',43,40,true],['New Customers',12,15,false],['Avg Deal Size','TZS 6M','TZS 5M',true]];
      foreach($targets as [$l,$a,$t,$met]):?>
      <div class="stat"><span class="text-[12px] font-medium text-slate-600"><?=$l?></span>
        <div class="text-right"><div class="text-[12.5px] font-bold text-[#111827]"><?=$a?></div>
          <div class="text-[10px] <?=$met?'text-green-500':'text-red-400'?>"><?=$met?'✓ Above':'↓ Below'?> target (<?=$t?>)</div></div></div>
      <?php endforeach;?>
    </div>
  </div>
</div>
<script>
new Chart(document.getElementById('an-rev'),{type:'line',data:{labels:['Jan','Feb','Mar','Apr','May','Jun','Jul'],
  datasets:[{label:'Revenue',data:[18.4,22.1,19.8,25.6,28.9,31.2,34.8],borderColor:'#16A34A',backgroundColor:'rgba(22,163,74,.1)',fill:true,tension:.4,borderWidth:2.5}]},
  options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{ticks:{callback:v=>v+'M',font:{size:10}}}}}});
new Chart(document.getElementById('an-cat'),{type:'bar',data:{labels:['Electronics','FMCG','Hardware','Apparel','Other'],
  datasets:[{data:[38,27,19,11,5],backgroundColor:['#16A34A','#2563EB','#D97706','#7C3AED','#94A3B8'],borderRadius:6}]},
  options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{ticks:{callback:v=>v+'%',font:{size:10}}}}}});
</script>
