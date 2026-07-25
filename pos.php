<div class="space-y-4">
  <div class="section-header"><h2>Point of Sale</h2>
    <button class="btn btn-primary" onclick="openCheckout()"><i data-lucide="shopping-bag" class="w-3.5 h-3.5"></i> New Sale</button>
  </div>
  <div id="pos-kpis" class="grid-4" style="gap:12px"></div>
  <div class="grid-2" style="gap:16px">
    <div><h3 class="text-[14px] font-bold text-[#111827] mb-3">Today's Transactions</h3><div id="pos-table"></div></div>
    <div class="card">
      <h3 class="text-[14px] font-bold text-[#111827] mb-3">Quick Sale</h3>
      <div class="space-y-2" id="cart"></div>
      <div class="border-t border-slate-100 mt-3 pt-3">
        <div class="flex justify-between font-bold text-[14px]"><span>Total:</span><span id="cart-total"><?= CURRENCY ?> 0</span></div>
        <div class="grid-2 mt-3" style="gap:8px">
          <button class="btn btn-secondary" onclick="clearCart()">Clear</button>
          <button class="btn btn-primary" onclick="checkout()">Checkout</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Checkout Modal -->
  <div id="checkout-modal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="card w-full max-w-md">
      <div class="flex items-center justify-between mb-4"><h3 class="font-bold text-[15px] text-[#111827]">New Sale</h3>
        <button onclick="document.getElementById('checkout-modal').classList.add('hidden')" class="text-slate-400"><i data-lucide="x" class="w-4 h-4"></i></button></div>
      <div class="space-y-3">
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Customer (optional)</label><input id="pos-cust" class="bs-input" placeholder="Walk-in customer"/></div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Items / Description *</label><input id="pos-items" class="bs-input" placeholder="e.g. 2x Rice, 1x Oil"/></div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Amount (<?= CURRENCY ?>) *</label><input id="pos-amount" type="number" class="bs-input" placeholder="0"/></div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Payment Method</label>
          <select id="pos-method" class="bs-input"><option>Cash</option><option>M-Pesa</option><option>Card</option><option>Airtel Money</option></select></div>
        <div class="flex gap-2 pt-2"><button class="btn btn-primary flex-1" onclick="processSale()">Process Sale</button>
          <button class="btn btn-secondary" onclick="document.getElementById('checkout-modal').classList.add('hidden')">Cancel</button></div>
      </div>
    </div>
  </div>
</div>
<script>
let transactions=[];
let cartItems=[];
(async()=>{
  const rows=await fetchRows('pos_transactions',{},100);
  transactions=rows.length?rows:[
    {id:1,customer:'Walk-in',items:'Rice 2kg x3, Oil 5L x1',amount:31500,method:'Cash',time:'09:15'},
    {id:2,customer:'James K.',items:'Samsung TV 43"',amount:850000,method:'M-Pesa',time:'10:30'},
    {id:3,customer:'Walk-in',items:'Cement x5',amount:95000,method:'Cash',time:'11:45'},
    {id:4,customer:'Amina R.',items:'LG Fridge',amount:1650000,method:'Card',time:'14:20'},
    {id:5,customer:'Walk-in',items:'Cooking Oil x4',amount:66000,method:'M-Pesa',time:'16:05'},
  ];
  renderPOS();
})();
function renderPOS(){
  const total=transactions.reduce((s,t)=>s+(t.amount||0),0);
  const cash=transactions.filter(t=>t.method==='Cash').reduce((s,t)=>s+(t.amount||0),0);
  document.getElementById('pos-kpis').innerHTML=
    buildKpi('Sales Today',String(transactions.length),'',null,'#2563EB')+
    buildKpi('Revenue Today',money(total),'+8%',true,'#16A34A')+
    buildKpi('Cash Sales',money(cash),'',null,'#F59E0B')+
    buildKpi('Avg Sale',money(total/transactions.length||0),'',null,'#7C3AED');
  document.getElementById('pos-table').innerHTML=buildTable(
    ['#','Customer','Items','Amount','Method','Time'],transactions,
    t=>`<tr><td class="font-mono text-[11px] text-slate-400">#${t.id}</td>
      <td class="font-medium text-[#111827]">${t.customer||'Walk-in'}</td>
      <td class="text-[11px] text-slate-500">${t.items||t.description||''}</td>
      <td class="font-bold">${money(t.amount)}</td>
      <td>${buildPill(t.method,t.method==='Cash'?'#16A34A':t.method==='M-Pesa'?'#10B981':'#2563EB')}</td>
      <td class="text-slate-400">${t.time||t.created_at?.slice(11,16)||''}</td></tr>`
  );lucide.createIcons();
}
function openCheckout(){document.getElementById('checkout-modal').classList.remove('hidden');}
async function processSale(){
  const items=document.getElementById('pos-items').value.trim();
  const amount=Number(document.getElementById('pos-amount').value);
  if(!items||!amount){toast('Items and amount required','error');return;}
  const t={id:uid(),customer:document.getElementById('pos-cust').value||'Walk-in',items,amount,method:document.getElementById('pos-method').value,time:new Date().toTimeString().slice(0,5)};
  transactions.unshift(t);
  try{await insertRow('pos_transactions',{customer_name:t.customer,description:items,total_amount:amount,payment_method:t.method});toast('Sale recorded!');}
  catch(e){toast('Sale added locally');}
  document.getElementById('checkout-modal').classList.add('hidden');renderPOS();
}
function clearCart(){cartItems=[];renderCart();}
function renderCart(){
  document.getElementById('cart').innerHTML=cartItems.length?cartItems.map((c,i)=>`
    <div class="flex justify-between items-center py-1.5 border-b border-slate-50">
      <span class="text-[12.5px] font-medium text-[#111827]">${c.name}</span>
      <div class="flex items-center gap-2"><span class="text-[12.5px] font-bold">${money(c.price)}</span>
        <button onclick="cartItems.splice(${i},1);renderCart()" class="text-slate-400 hover:text-red-500"><i data-lucide="x" class="w-3 h-3"></i></button></div>
    </div>`).join(''):
    '<p class="text-[12px] text-slate-400 text-center py-4">Cart is empty. Add products to sell.</p>';
  const total=cartItems.reduce((s,c)=>s+c.price,0);
  document.getElementById('cart-total').textContent='<?= CURRENCY ?> '+fmt(total);
  lucide.createIcons();
}
function checkout(){if(!cartItems.length){toast('Cart is empty','error');return;}openCheckout();}
renderCart();
</script>
