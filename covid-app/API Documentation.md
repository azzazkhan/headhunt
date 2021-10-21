# Provider API Routes

## [POST] **/api/certificate**

### **Data required**

Parameter Name | Expected Value   | Type
-------------- | ---------------- | ----
certificate    | File (Form Data) | Request body

### **Success Response**

Field Name             | Value                  | Type
---------------------- | ---------------------- | -----------
status                 | true                   | Boolean
data                   | [Object]               | JSON Object
data.certificate       | [Object]               | JSON Object
data.certificate.ref   | Certificate ID         | String
data.certificate.image | Image URL              | String


### **Error Cases**

An error response will be send if the following conditions are met.

1. Provider has exceeded the max certificate upload attempts. **[406 Not Acceptable]**
2. Provider is trying to submit new certificate but either has already submitted a certificate recently which is not approved yet (pending) or his/her certificate was already approved. **[406 Not Acceptable]**
3. File (certificate) was not sent with the request or was not uploaded properly. **[400 Bad Request]**
4. Uploaded file extension must match (png, jpg, jpeg, webp, bmp) **[400 Bad Request]**
5. Server could not save the uploaded file to specified destination. **[500 Internal Server Error]**
6. Failed to create new record in database. **[500 Internal Server Error]**

____________________________________________________________

## [GET]  /api/certificate/my-certificate
## [GET]  /api/certificate/{certificateID}