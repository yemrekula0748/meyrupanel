<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://integration.hepsijet.com/delivery/sendDeliveryOrderEnhanced',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
  "company": {
    "name": "SEMRE BUTIK",
    "abbreviationCode": "SMRBTK"
  },
  "serviceType": [
    "POD"
  ],
  "delivery": {
    "customerDeliveryNo": "SMR900127122032",
    "customerOrderId": "SMR900127122032",
    "deliveryDateOriginal": "2024-29-12",
    "totalParcels": "1",
    "desi": "4",
    "deliverySlotOriginal": "0",
    "deliveryType": "RETAIL",
    "product": {
      "productCode": "HX_STD"
    },
    "senderAddress": {
      "companyAddressId": "semr-smrbtk-703",
      "country": {
        "name": "Antalya"
      },
      "city": {
        "name": "Kepez"
      },
      "town": {
        "name": "Kepez"
      },
      "district": {
        "name": "BARAJ"
      },
      "addressLine1": "Kuştepe Mah. Mecidiyeköy Yolu Cad. Trump Towers Kule 2 Kat:3 No:14 34387 Şişli / İstanbul"
    },

      "receiver": {
      "companyCustomerId": "SMR900127122032",
      "firstName": "Mehmet",
      "lastName": "Polat",
      "phone1": "5555555555",
      "email": "mehmet.polat851909@gmail.com"
    },
    "recipientAddress": {
      "companyAddressId": "SMR900127122032",
      "country": {
        "name": "Türkiye"
      },
      "city": {
        "name": "Antalya"
      },
      "town": {
        "name": "KEPEZ"
      },
      "district": {
        "name": "Hürriyet"
      },
      "addressLine1": "Hürriyet, 1662. Sk., 35763 Ödemiş/İzmir"
    },
    "recipientPerson": "yunus emre",
    "recipientPersonPhone1": "5555555555"
  },
  "deliveryAmountList": [
    {
      "amount": "3500",
      "description": "Hizmet Bedeli",
      "type": "SERVICE_AMOUNT",
      "currency": "TRY"
    }
  ]
}
',
  CURLOPT_HTTPHEADER => array(
    'X-Auth-Token: aaf807a9-2cea-43be-8855-5db9fc06bb58',
    'Content-Type: application/json',
    'Authorization: Basic c2VtcmVidXRpa19pbnRlZ3JhdGlvbjphZG1pbjEyMw=='
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
