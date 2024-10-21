<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture - {{ $facture->numero_facture }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .facture-details {
            margin-top: 20px;
        }
        .total {
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Facture N° {{ $facture->numero_facture }}</h1>
        <p>Date : {{ $facture->date_facture }}</p>
    </div>

    <div class="facture-details">
        <h3>Détails du client</h3>
        <p>Nom : {{ $facture->vente->client->nom }}</p>
        <p>Email : {{ $facture->vente->client->email }}</p>

        <h3>Détails de la vente</h3>
        <table>
            <thead>
                <tr>
                    <th>Médicament</th>
                    <th>Quantité</th>
                    <th>Prix Unitaire</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($facture->vente->medicaments as $medicament)
                    <tr>
                        <td>{{ $medicament->nom }}</td>
                        <td>{{ $medicament->pivot->quantite }}</td>
                        <td>{{ $medicament->pivot->prix_unitaire }} FCFA</td>
                        <td>{{ $medicament->pivot->quantite * $medicament->pivot->prix_unitaire }} FCFA</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="total">
        <h3>Montant Total : {{ $facture->montant_total }} FCFA</h3>
    </div>
</body>
</html>
