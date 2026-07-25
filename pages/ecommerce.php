<div class="space-y-4">
  <div class="section-header"><h2>E-Commerce Orders</h2>
    <button class="btn btn-secondary" onclick="location.reload()"><i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i> Refresh</button></div>
  <div id="ecom-kpis" class="grid-4" style="gap:12px">
    <div class="kpi"><div class="spinner"></div></div><div class="kpi"><div class="spinner"></div></div>
    <div class="kpi"><div class="spinner"></div></div><div class="kpi"><div class="spinner"></div></div></div>
  <div id="ecom-table"></div>
</div>
<script>
let eoorders=[];
const EOSC={Pending:'#F59E0B',Processing:'#2563EB',Shipped:'#7C3AED',Delivered:'#16A34A',Cancelled:'#EF4444'};
(async()=>{
  const rows=await fetchRows('ecom_orders');
  eoorders=rows.length?rows:[
    {id:'ORD-5021',customer:'Juma Hassan',product:'Samsung TV 55"',amount:1200000,status:'Delivered',date:'2026-07-20'},
    {id:'ORD-5020',customer:'Mwajuma Said',product:'LG Fridge 400L',amount:1650000,status:'Shipped',date:'2026-07-19'},
    {id:'ORD-5019',customer:'Ali Mohamed',product:'Cooking Oil 5L x10',amount:165000,status:'Processing',date:'2026-07-18'},
    {id:'ORD-5018',customer:'Fatima Ally',product:'Cement 50kg x20',amount:380000,status:'Pending',date:'2026-07-17'},
    {id:'ORD-5017',customer:'Hassan Juma',product:'Iron Sheet 4m x5',amount:180000,status:'Cancelled',date:'2026-07-16'},
  ];
  const total=eoorders.reduce((s,o)=>s+(o.amount||0),0);
  const pending=eoorders.filter(o=>o.status==='Pending'||o.status==='Processing').length;
  const delivered=eoorders.filter(o=>o.status==='Delivered').length;
  document.getElementById('ecom-kpis').innerHTML=
    buildKpi('Total Orders',String(eoorders.length),'+12',true,'#2563EB')+
    buildKpi('Revenue',money(total),'+8%',true,'#16A34A')+
    buildKpi('Pending',String(pending),'',null,'#F59E0B')+
    buildKpi('Delivered',String(delivered),'+5',true,'#7C3AED');
  document.getElementById('ecom-table').innerHTML=buildTable(
    ['Order #','Customer','Product','Amount','Status','Date',''],
    eoorders,o=>`<tr>
      <td class="font-mono font-semibold text-[#111827]">${o.id||o.order_number}</td>
      <td class="font-medium">${o.customer||o.customer_name}</td>
      <td class="text-slate-500">${o.product||o.product_name||''}</td>
      <td class="font-bold">${money(o.amount||o.total_amount)}</td>
      <td>${buildPill(o.status,EOSC[o.status]||'#94A3B8')}</td>
      <td class="text-slate-500">${o.date||o.order_date||''}</td>
      <td><select onchange="updateOrderStatus('${o.id}',this.value)" class="text-[11px] border border-slate-200 rounded-lg px-2 py-1 outline-none">
        ${['Pending','Processing','Shipped','Delivered','Cancelled'].map(s=>`<option ${s===o.status?'selected':''} value="${s}">${s}</option>`).join('')}
      </select></td>
    </tr>`);
  lucide.createIcons();
})();
function updateOrderStatus(id,status){
  eoorders=eoorders.map(o=>o.id==id?{...o,status}:o);
  updateRow('ecom_orders',id,{status}).catch(()=>{});
  toast('Status updated: '+status);
}
</script>