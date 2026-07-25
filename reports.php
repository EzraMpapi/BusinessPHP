<div class="space-y-4">
  <div class="section-header"><h2>Reports & Analytics</h2>
    <button class="btn btn-primary" onclick="window.print()"><i data-lucide="printer" class="w-3.5 h-3.5"></i> Print Report</button>
  </div>
  <!-- Report tabs -->
  <div class="flex gap-2 flex-wrap">
    <?php foreach(['P&L','Balance Sheet','AR Aging','Expenses','Payroll','Tax Summary'] as $tab): ?>
    <button onclick="switchReport('<?= $tab ?>')" class="report-tab btn btn-secondary text-[12px]"><?= $tab ?></button>
    <?php endforeach; ?>
  </div>
  <div id="report-content" class="space-y-4"></div>
</div>
<script>
const MONTHS=['Jan','Feb','Mar','Apr','May','Jun','Jul'];
const REV=[18400000,22100000,19800000,25600000,28900000,31200000,34800000];
const EXP=[11200000,13400000,12100000,14800000,16200000,17500000,18900000];

const REPORTS = {
  'P&L': ()=>`
    <div class="card">
      <h3 class="text-[14px] font-bold text-[#111827] mb-4">Profit & Loss Statement — July 2026</h3>
      <div class="space-y-0">
        ${[['Revenue','#16A34A',34800000],['Cost of Goods Sold','#EF4444',14200000],['Gross Profit','#2563EB',20600000],
           ['Operating Expenses','#EF4444',9300000],['Net Profit','#16A34A',11300000]]
          .map(([l,c,v])=>`<div class="stat"><span class="text-[13px] font-semibold text-[#111827]">${l}</span><span class="text-[14px] font-bold" style="color:${c}">${money(v)}</span></div>`).join('')}
      </div>
    </div>
    <div class="card"><canvas id="rep-chart" height="150"></canvas></div>`,

  'Balance Sheet': ()=>`
    <div class="grid-2" style="gap:16px">
      <div class="card">
        <h3 class="text-[14px] font-bold text-[#111827] mb-3">Assets</h3>
        ${[['Cash & Bank','28,400,000'],['Accounts Receivable','12,100,000'],['Inventory','89,400,000'],['Fixed Assets','45,000,000'],['<strong>Total Assets</strong>','174,900,000']]
          .map(([l,v])=>`<div class="stat"><span class="text-[12.5px] text-slate-600">${l}</span><span class="font-bold text-[#111827]"><?= CURRENCY ?> ${v}</span></div>`).join('')}
      </div>
      <div class="card">
        <h3 class="text-[14px] font-bold text-[#111827] mb-3">Liabilities & Equity</h3>
        ${[['Accounts Payable','8,200,000'],['Short-term Loans','15,000,000'],["Owner's Equity","151,700,000"],['<strong>Total L + E</strong>','174,900,000']]
          .map(([l,v])=>`<div class="stat"><span class="text-[12.5px] text-slate-600">${l}</span><span class="font-bold text-[#111827]"><?= CURRENCY ?> ${v}</span></div>`).join('')}
      </div>
    </div>`,

  'AR Aging': ()=>`
    <div class="card">
      <h3 class="text-[14px] font-bold text-[#111827] mb-4">Accounts Receivable Aging</h3>
      ${buildTable(['Customer','Invoice','Amount','Days Overdue','Status'],
        [{c:'Mwangi Supplies',inv:'INV-2847',amt:2400000,days:4,s:'Current'},
         {c:'Dar Tech Solutions',inv:'INV-2844',amt:8500000,days:12,s:'1-30 Days'},
         {c:'Karibu Supermarket',inv:'INV-2846',amt:5800000,days:35,s:'31-60 Days'}],
        r=>`<tr><td class="font-medium text-[#111827]">${r.c}</td><td class="font-mono">${r.inv}</td>
          <td class="font-bold">${money(r.amt)}</td><td>${r.days} days</td>
          <td>${buildPill(r.s,r.days<30?'#16A34A':r.days<60?'#F59E0B':'#EF4444')}</td></tr>`)}
    </div>`,

  'Expenses': ()=>`
    <div class="card">
      <h3 class="text-[14px] font-bold text-[#111827] mb-4">Expense Summary — July 2026</h3>
      ${buildTable(['Category','Amount','% of Total'],
        [['Salaries',8500000,45],['Rent & Utilities',2850000,15],['Marketing',1900000,10],
         ['Equipment',1520000,8],['Travel',760000,4],['Other',3770000,18]],
        r=>`<tr><td class="font-medium text-[#111827]">${r[0]}</td><td class="font-bold">${money(r[1])}</td>
          <td><div class="flex items-center gap-2"><div class="flex-1 h-1.5 bg-slate-100 rounded-full"><div class="h-full bg-[#16A34A] rounded-full" style="width:${r[2]}%"></div></div><span class="text-[11px] text-slate-500">${r[2]}%</span></div></td></tr>`)}
    </div>`,

  'Payroll': ()=>`
    <div class="card">
      <h3 class="text-[14px] font-bold text-[#111827] mb-4">Payroll Summary — July 2026</h3>
      ${buildTable(['Employee','Gross','PAYE','NSSF','NHIF','Net Pay'],
        [{n:'Amina Said',g:850000,t:127500,nssf:85000,nhif:17000,net:620500},
         {n:'John Mwangi',g:700000,t:91000,nssf:70000,nhif:14000,net:525000},
         {n:'Grace Kimani',g:650000,t:78000,nssf:65000,nhif:13000,net:494000},
         {n:'Peter Ally',g:450000,t:36000,nssf:45000,nhif:9000,net:360000}],
        r=>`<tr><td class="font-medium text-[#111827]">${r.n}</td><td>${money(r.g)}</td>
          <td class="text-red-500">${money(r.t)}</td><td class="text-red-500">${money(r.nssf)}</td>
          <td class="text-red-500">${money(r.nhif)}</td><td class="font-bold text-green-600">${money(r.net)}</td></tr>`)}
    </div>`,

  'Tax Summary': ()=>`
    <div class="card">
      <h3 class="text-[14px] font-bold text-[#111827] mb-4">TRA Tax Summary — Q2 2026</h3>
      ${[['VAT Collected',6960000],['VAT Paid (Input)',2840000],['Net VAT Payable',4120000],['Corporate Tax Estimate',3390000],['PAYE Total',332500],['NSSF Total',265000],['Total Tax Obligations',8107500]]
        .map(([l,v])=>`<div class="stat"><span class="text-[13px] font-semibold text-[#111827]">${l}</span><span class="text-[14px] font-bold text-[#111827]">${money(v)}</span></div>`).join('')}
    </div>`
};

function switchReport(name){
  document.querySelectorAll('.report-tab').forEach(b=>b.classList.toggle('btn-primary',b.textContent===name));
  document.querySelectorAll('.report-tab').forEach(b=>b.classList.toggle('btn-secondary',b.textContent!==name));
  document.getElementById('report-content').innerHTML=REPORTS[name]?REPORTS[name]():'<p>Report coming soon</p>';
  lucide.createIcons();
  if(name==='P&L'){
    setTimeout(()=>{
      new Chart(document.getElementById('rep-chart'),{type:'bar',data:{labels:MONTHS,
        datasets:[{label:'Revenue',data:REV,backgroundColor:'#16A34A',borderRadius:4},
                  {label:'Expenses',data:EXP,backgroundColor:'#EF4444',borderRadius:4}]},
        options:{responsive:true,plugins:{legend:{labels:{font:{size:11}}}},
          scales:{y:{ticks:{callback:v=>(v/1000000).toFixed(0)+'M',font:{size:10}}}}}}); },100);
  }
}
switchReport('P&L');
</script>
