<div class="space-y-4">
  <div class="section-header"><h2>Documents</h2>
    <button class="btn btn-primary" onclick="openDocForm()"><i data-lucide="upload-cloud" class="w-3.5 h-3.5"></i> Upload Document</button></div>
  <div class="grid-4" style="gap:12px">
    <div class="card text-center cursor-pointer hover:border-green-300" onclick="filterDocs('all')">
      <i data-lucide="folder-open" class="w-6 h-6 text-[#16A34A] mx-auto mb-1"></i>
      <p class="text-[12px] font-bold text-[#111827]" id="cnt-all">All Files</p></div>
    <div class="card text-center cursor-pointer hover:border-blue-300" onclick="filterDocs('contract')">
      <i data-lucide="file-check" class="w-6 h-6 text-blue-500 mx-auto mb-1"></i>
      <p class="text-[12px] font-bold text-[#111827]">Contracts</p></div>
    <div class="card text-center cursor-pointer hover:border-purple-300" onclick="filterDocs('invoice')">
      <i data-lucide="receipt-text" class="w-6 h-6 text-purple-500 mx-auto mb-1"></i>
      <p class="text-[12px] font-bold text-[#111827]">Invoices</p></div>
    <div class="card text-center cursor-pointer hover:border-orange-300" onclick="filterDocs('report')">
      <i data-lucide="bar-chart-3" class="w-6 h-6 text-orange-500 mx-auto mb-1"></i>
      <p class="text-[12px] font-bold text-[#111827]">Reports</p></div>
  </div>
  <div id="docs-table"></div>
  <div id="doc-modal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="card w-full max-w-md"><div class="flex items-center justify-between mb-4"><h3 class="font-bold text-[15px]">Upload Document</h3>
      <button onclick="document.getElementById('doc-modal').classList.add('hidden')" class="text-slate-400"><i data-lucide="x" class="w-4 h-4"></i></button></div>
      <div class="space-y-3">
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Document Name *</label><input id="doc-name" class="bs-input" placeholder="e.g. Q3 Financial Report"/></div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Category</label>
          <select id="doc-cat" class="bs-input"><option value="contract">Contract</option><option value="invoice">Invoice</option><option value="report">Report</option><option value="other">Other</option></select></div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Notes</label><input id="doc-notes" class="bs-input" placeholder="Optional notes"/></div>
        <div class="flex gap-2 pt-2">
          <button class="btn btn-primary flex-1" onclick="saveDoc()">Add Document</button>
          <button class="btn btn-secondary" onclick="document.getElementById('doc-modal').classList.add('hidden')">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
let docs=[];let activeFilter='all';
const DOC_ICONS={contract:'file-check',invoice:'receipt-text',report:'bar-chart-3',other:'file-text'};
const DOC_COLORS={contract:'#2563EB',invoice:'#7C3AED',report:'#F59E0B',other:'#64748B'};
(async()=>{
  const rows=await fetchRows('documents');
  docs=rows.length?rows:[
    {id:1,name:'Supplier Contract - Karibu Wholesalers',category:'contract',size:'245 KB',date:'2026-07-15',notes:'Renewal due Dec 2026'},
    {id:2,name:'Q2 Financial Report 2026',category:'report',size:'1.2 MB',date:'2026-07-10',notes:'Board approved'},
    {id:3,name:'Invoice INV-2847',category:'invoice',size:'89 KB',date:'2026-07-20',notes:''},
    {id:4,name:'Employment Contract - Amina Said',category:'contract',size:'178 KB',date:'2022-03-01',notes:'Permanent staff'},
    {id:5,name:'Annual Audit Report 2025',category:'report',size:'3.4 MB',date:'2026-01-15',notes:'Certified by PWC'},
  ];renderDocs();
})();
function filterDocs(cat){activeFilter=cat;renderDocs();}
function renderDocs(){
  const filtered=activeFilter==='all'?docs:docs.filter(d=>d.category===activeFilter);
  document.getElementById('docs-table').innerHTML=buildTable(
    ['Document','Category','Size','Date','Notes',''],
    filtered,d=>`<tr>
      <td><div class="flex items-center gap-2">
        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:${DOC_COLORS[d.category||'other']}18">
          <i data-lucide="${DOC_ICONS[d.category||'other']}" class="w-3.5 h-3.5" style="color:${DOC_COLORS[d.category||'other']}"></i></div>
        <span class="font-medium text-[#111827]">${d.name||d.doc_name||''}</span></div></td>
      <td>${buildPill(d.category||'other',DOC_COLORS[d.category||'other'])}</td>
      <td class="text-slate-500">${d.size||'—'}</td>
      <td class="text-slate-500">${d.date||d.upload_date||''}</td>
      <td class="text-slate-400 text-[11px]">${d.notes||''}</td>
      <td><button onclick="deleteDoc(${d.id})" class="text-slate-400 hover:text-red-500"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i></button></td>
    </tr>`);
  lucide.createIcons();
}
function openDocForm(){document.getElementById('doc-modal').classList.remove('hidden');}
async function saveDoc(){
  const name=document.getElementById('doc-name').value.trim();
  if(!name){toast('Name required','error');return;}
  const d={id:uid(),name,category:document.getElementById('doc-cat').value,
    size:'—',date:today(),notes:document.getElementById('doc-notes').value};
  docs.unshift(d);
  try{await insertRow('documents',{doc_name:d.name,...d});toast('Document added!');}catch(e){toast('Added locally');}
  document.getElementById('doc-modal').classList.add('hidden');renderDocs();
}
function deleteDoc(id){
  if(!confirm('Delete this document?'))return;
  docs=docs.filter(d=>d.id!=id);
  deleteRow('documents',id).catch(()=>{});
  renderDocs();toast('Deleted');
}
</script>