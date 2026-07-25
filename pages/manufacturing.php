<div class="space-y-4">
  <div class="section-header"><h2>Manufacturing</h2>
    <button class="btn btn-primary" onclick="openWOForm()"><i data-lucide="plus" class="w-3.5 h-3.5"></i> New Work Order</button>
  </div>
  <div id="mfg-kpis" class="grid-4" style="gap:12px"></div>
  <div class="grid-2" style="gap:16px">
    <div><h3 class="text-[14px] font-bold text-[#111827] mb-3">Work Orders</h3><div id="wo-table"></div></div>
    <div><h3 class="text-[14px] font-bold text-[#111827] mb-3">Bill of Materials</h3><div id="bom-table"></div></div>
  </div>
  <div id="wo-modal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="card w-full max-w-sm">
      <div class="flex items-center justify-between mb-4"><h3 class="font-bold text-[15px] text-[#111827]">New Work Order</h3>
        <button onclick="document.getElementById('wo-modal').classList.add('hidden')" class="text-slate-400"><i data-lucide="x" class="w-4 h-4"></i></button></div>
      <div class="space-y-3">
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Product *</label><input id="wo-product" class="bs-input" placeholder="Product name"/></div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Quantity</label><input id="wo-qty" type="number" class="bs-input" placeholder="100"/></div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Due Date</label><input id="wo-due" type="date" class="bs-input" value="<?= date('Y-m-d', strtotime('+7 days')) ?>"/></div>
        <div class="flex gap-2 pt-2"><button class="btn btn-primary flex-1" onclick="saveWO()">Create</button>
          <button class="btn btn-secondary" onclick="document.getElementById('wo-modal').classList.add('hidden')">Cancel</button></div>
      </div>
    </div>
  </div>
</div>
<script>
const WO_STATUS={Planned:'#94A3B8',InProgress:'#F59E0B',Completed:'#16A34A',OnHold:'#EF4444'};
let workOrders=[
  {id:1,product:'Assembled TVs',qty:50,completed:35,status:'InProgress',due:'2026-07-25'},
  {id:2,product:'Packaged Rice 2kg',qty:500,completed:500,status:'Completed',due:'2026-07-20'},
  {id:3,product:'Assembled Fridges',qty:20,completed:0,status:'Planned',due:'2026-07-30'},
];
const boms=[
  {product:'Assembled TV',components:'TV Panel, Circuit Board, Remote, Cables, Casing',cost:650000},
  {product:'Packaged Rice',components:'Raw Rice 2.1kg, Packaging Bag, Label',cost:3800},
  {product:'Assembled Fridge',components:'Compressor, Thermostat, Body, Shelves, Seal',cost:950000},
];
function renderMfg(){
  const completed=workOrders.filter(w=>w.status==='Completed').length;
  document.getElementById('mfg-kpis').innerHTML=
    buildKpi('Work Orders',String(workOrders.length),'',null,'#2563EB')+
    buildKpi('In Progress',String(workOrders.filter(w=>w.status==='InProgress').length),'',null,'#F59E0B')+
    buildKpi('Completed',String(completed),'',null,'#16A34A')+
    buildKpi('Efficiency','87%','+3%',true,'#7C3AED');
  document.getElementById('wo-table').innerHTML=buildTable(['Product','Qty','Progress','Status','Due'],workOrders,w=>{
    const pct=Math.round((w.completed/w.qty)*100)||0;
    return `<tr><td class="font-semibold text-[#111827]">${w.product}</td><td>${w.qty}</td>
      <td><div class="flex items-center gap-2"><div style="flex:1;height:6px;background:#F1F5F9;border-radius:3px"><div style="width:${pct}%;height:100%;background:#16A34A;border-radius:3px"></div></div><span class="text-[11px] text-slate-500">${pct}%</span></div></td>
      <td>${buildPill(w.status.replace('InProgress','In Progress'),WO_STATUS[w.status])}</td>
      <td class="text-slate-500">${w.due}</td></tr>`;
  });
  document.getElementById('bom-table').innerHTML=buildTable(['Product','Components','Unit Cost'],boms,b=>`<tr>
    <td class="font-semibold text-[#111827]">${b.product}</td>
    <td class="text-[11px] text-slate-500">${b.components}</td>
    <td class="font-bold">${money(b.cost)}</td></tr>`);
  lucide.createIcons();
}
function openWOForm(){document.getElementById('wo-modal').classList.remove('hidden');}
function saveWO(){
  const product=document.getElementById('wo-product').value.trim();
  if(!product){toast('Product required','error');return;}
  workOrders.unshift({id:uid(),product,qty:Number(document.getElementById('wo-qty').value)||1,completed:0,status:'Planned',due:document.getElementById('wo-due').value});
  document.getElementById('wo-modal').classList.add('hidden');
  renderMfg();toast('Work order created');
}
renderMfg();
</script>
