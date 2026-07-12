<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #222; }
    h1 { font-size: 16px; text-align: center; margin-bottom: 0; }
    .sub { text-align: center; color: #666; margin-top: 2px; margin-bottom: 16px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { padding: 4px 6px; text-align: left; }
    th { border-bottom: 1px solid #222; }
    tr.line td { border-bottom: 1px solid #ddd; }
    .right { text-align: right; }
    .total-row td { border-top: 2px solid #222; font-weight: bold; }
    .meta { margin-bottom: 4px; }
</style>
</head>
<body>
    <h1>Recu de vente - Pharmacie</h1>
    <div class="sub"><?= esc($sale['receipt_number'] ?? ('#' . $sale['id_purchase'])) ?></div>

    <div class="meta">Date : <?= esc($sale['created_at'] ?? '') ?></div>
    <?php if(!empty($sale['payment_method'])): ?>
        <div class="meta">Moyen de paiement : <?= esc($sale['payment_method']) ?></div>
    <?php endif; ?>
    <?php if(!empty($cashier)): ?>
        <div class="meta">Vendu par : <?= esc(trim($cashier['name_user'] . ' ' . $cashier['surname_user'])) ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th class="right">Qte</th>
                <th class="right">Prix unitaire</th>
                <th class="right">Sous-total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($products as $line): ?>
                <tr class="line">
                    <td><?= esc($line['name_product']) ?></td>
                    <td class="right"><?= esc($line['quantity']) ?></td>
                    <td class="right"><?= esc($line['unit_price']) ?></td>
                    <td class="right"><?= esc($line['unit_price'] * $line['quantity']) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="3">Total</td>
                <td class="right"><?= esc($sale['final_amount']) ?></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
