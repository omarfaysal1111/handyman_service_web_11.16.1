const fs = require('fs');

const file = './Handyman_Service_API.postman_collection.json';
const data = JSON.parse(fs.readFileSync(file, 'utf8'));

// 1. Update Registration to include providertype_id
const authFolder = data.item.find(i => i.name === 'Authentication');
if (authFolder) {
    const registerReq = authFolder.item.find(i => i.name === 'Register');
    if (registerReq && registerReq.request && registerReq.request.body && registerReq.request.body.formdata) {
        const hasProviderType = registerReq.request.body.formdata.find(f => f.key === 'providertype_id');
        if (!hasProviderType) {
            registerReq.request.body.formdata.push({
                "key": "providertype_id",
                "value": "1",
                "description": "ID for Tourist Office or Tourism Company (If provider)",
                "type": "text"
            });
        }
    }
}

// 2. Add Save Manual Payment to Payments & Wallet
const paymentsFolder = data.item.find(i => i.name === 'Payments & Wallet');
if (paymentsFolder) {
    const hasManualPayment = paymentsFolder.item.find(i => i.name === 'Save Manual Payment');
    if (!hasManualPayment) {
        paymentsFolder.item.push({
            "name": "Save Manual Payment",
            "request": {
                "auth": {
                    "type": "bearer",
                    "bearer": [
                        {
                            "key": "token",
                            "value": "{{auth_token}}",
                            "type": "string"
                        }
                    ]
                },
                "method": "POST",
                "header": [
                    {
                        "key": "Accept",
                        "value": "application/json",
                        "type": "text"
                    }
                ],
                "body": {
                    "mode": "formdata",
                    "formdata": [
                        {
                            "key": "booking_id",
                            "value": "1",
                            "type": "text"
                        },
                        {
                            "key": "customer_id",
                            "value": "1",
                            "type": "text"
                        },
                        {
                            "key": "total_amount",
                            "value": "150",
                            "type": "text"
                        },
                        {
                            "key": "payment_type",
                            "value": "instapay",
                            "description": "instapay or vodafone_cash",
                            "type": "text"
                        },
                        {
                            "key": "receipt",
                            "type": "file",
                            "src": []
                        }
                    ]
                },
                "url": {
                    "raw": "{{base_url}}/api/save-manual-payment",
                    "host": [
                        "{{base_url}}"
                    ],
                    "path": [
                        "api",
                        "save-manual-payment"
                    ]
                }
            },
            "response": []
        });
    }
}

fs.writeFileSync(file, JSON.stringify(data, null, "\t"));
console.log("Postman collection updated successfully.");
