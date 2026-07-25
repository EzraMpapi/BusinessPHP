<div class="space-y-4">
  <div class="section-header"><h2>Supply Chain Management</h2>
    <button class="btn btn-primary" onclick="openShipForm()"><i data-lucide="plus" class="w-3.5 h-3.5"></i> New Shipment</button></div>
  <div id="scm-kpis" class="grid-3" style="gap:12px">
    <div class="kpi"><div class="spinner"></div></div><div class="kpi"><div class="spinner"></div></div><div class="kpi"><div class="spinner"></div></div></div>
  <div id="scm-table"></div>
  <div id="ship-modal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="card w-full max-w-md"><div class="flex items-center justify-between mb-4"><h3 class="font-bold text-[15px]">New Shipment</h3>
      <button onclick="document.getElementById('ship-modal').classList.add('hidden')" class="text-slate-400"><i data-lucide="x" class="w-4 h-4"></i></button></div>
      <div class="space-y-3">
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Supplier</label><input id="sh-supplier" class="bs-input" placeholder="Supplier name"/></div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Destination</label><input id="sh-dest" class="bs-input" placeholder="Delivery address"/></div>
        <div class="grid-2" style="gap:8px">
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Items</label><input id="sh-items" class="bs-input" placeholder="Description"/></div>
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Weight (kg)</label><input id="sh-weight" type="number" class="bs-input" placeholder="0"/></div>
        </div>
        <div class="flex gap-2 pt-2">
          <button class="btn btn-primary flex-1" onclick="saveShipment()">Create</button>
          <button class="btn btn-secondary" onclick="document.getElementById('ship-modal').classList.add('hidden')">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
let shipments=[];
const SC={Transit:'#F59E0B',Delivered:'#16A34A',Pending:'#94A3B8',Delayed:'#EF4444'};
(async()=>{
  const rows=await fetchRows('scm_shipments');
  shipments=rows.length?rows:[
    {id:'SHP-091',supplier:'Nairobi Distributors',destination:'Dar es Salaam Warehouse',items:'Electronics x 50',weight:340,status:'Transit',date:'2026-07-20'},
    {id:'SHP-090',supplier:'Mombasa Port Agents',destination:'Arusha Branch',items:'FMCG Goods',weight:820,status:'Delivered',date:'2026-07-18'},
    {id:'SHP-089',supplier:'China Direct Imports',destination:'Main Warehouse',items:'Hardware Tools x 200',weight:1200,status:'Transit',date:'2026-07-15'},
    {id:'SHP-088',supplier:'Uganda Suppliers',destination:'Mwanza Branch',items:'Agricultural Products',weight:500,status:'Delayed',date:'2026-07-12'},
  ];render();
})();
function render(){
  const transit=shipments.filter(s=>s.status==='Transit').length;
  const delivered=shipments.filter(s=>s.status==='Delivered').length;
  const delayed=shipments.filter(s=>s.status==='Delayed').length;
  document.getElementById('scm-kpis').innerHTML=
    buildKpi('Active Shipments',String(transit),'',null,'#2563EB')+
    buildKpi('Delivered This Month',String(delivered),'+3',true,'#16A34A')+
    buildKpi('Delayed',String(delayed),'',null,'#EF4444');
  document.getElementById('scm-table').innerHTML=buildTable(
    ['Shipment #','Supplier','Destination','Items','Weight','Status','Date'],
    shipments,s=>`<tr>
      <td class="font-mono font-semibold text-[#111827]">${s.id||s.tracking_number}</td>
      <td class="font-medium">${s.supplier||s.supplier_name}</td>
      <td class="text-slate-500">${s.destination}</td>
      <td class="text-slate-500">${s.items||s.description||''}</td>
      <td>${(s.weight||0).toLocaleString()} kg</td>
      <td>${buildPill(s.status,SC[s.status]||'#94A3B8')}</td>
      <td class="text-slate-500">${s.date||s.ship_date||''}</td>
    </tr>`);
  lucide.createIcons();
}
function openShipForm(){document.getElementById('ship-modal').classList.remove('hidden');}
async function saveShipment(){
  const supplier=document.getElementById('sh-supplier').value.trim();
  if(!supplier){toast('Supplier required','error');return;}
  const row={id:'SHP-'+uid(),supplier,destination:document.getElementById('sh-dest').value,
    items:document.getElementById('sh-items').value,weight:Number(document.getElementById('sh-weight').value)||0,
    status:'Pending',date:today()};
  shipments.unshift(row);
  try{await insertRow('scm_shipments',{supplier_name:row.supplier,...row});toast('Shipment created!');}catch(e){toast('Added locally');}
  document.getElementById('ship-modal').classList.add('hidden');render();
}
</script>