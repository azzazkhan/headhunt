# Provider API Routes

### Error Response Structure

Parameter Name | Value                | Type
-------------- | -------------------- | -------
success        | false                | Boolean
message        | Custom error message | String

## [POST] **/api/provider/certificate**

### **Data required**

Parameter Name | Expected Value   | Type
-------------- | ---------------- | ----
certificate    | File (Image)     | Request body

### **Success Response**

Field Name              | Value                  | Type
----------------------- | ---------------------- | ----------------------------------
success                 | true                   | Boolean
message                 | Custom Response        | String
data                    | [Object]               | JSON Object
data.certificate        | [Object]               | JSON Object
data.certificate.ref    | Certificate ID         | String
data.certificate.status | Certificate Status     | String (approved|rejected|pending)
data.certificate.image  | Image URL              | String


### **Error Cases**

An error response will be send if the following conditions are met.

1. Provider has exceeded the max certificate upload attempts. **[406 Not Acceptable]**
2. Provider is trying to submit new certificate but either has already submitted a certificate recently which is not approved yet (pending) or his/her certificate was already approved. **[406 Not Acceptable]**
3. File (certificate) was not sent with the request or was not uploaded properly. **[400 Bad Request]**
4. Uploaded file extension must match (png, jpg, jpeg, webp, bmp) **[400 Bad Request]**
5. Server could not save the uploaded file to specified destination. **[500 Internal Server Error]**
6. Failed to create new record in database. **[500 Internal Server Error]**

____________________________________________________________

## [GET]  **/api/provider/certificate/my-certificate**

### **Success Response**

Field Name              | Value                  | Type
----------------------- | ---------------------- | ----------------------------------
success                 | true                   | Boolean
message                 | Custom Response        | String
data                    | [Object]               | JSON Object
data.certificate        | [Object]               | JSON Object
data.certificate.ref    | Certificate ID         | String
data.certificate.status | Certificate Status     | String (approved|rejected|pending)
data.certificate.image  | Image URL              | String


### **Error Cases**

An error response will be send if the following conditions are met.

1. A JSON 404 response will be sent if the provider has no certificates. **[404 Not Found]**

____________________________________________________________
## [GET]  **/api/provider/certificate/{certificateID}**

### **Data required**

Parameter Name | Expected Value | Type
-------------- | -------------- | ---------------
certificateID  | Certificate ID | Route Parameter

### **Success Response**

Field Name              | Value                  | Type
----------------------- | ---------------------- | ----------------------------------
success                 | true                   | Boolean
message                 | Custom Response        | String
data                    | [Object]               | JSON Object
data.certificate        | [Object]               | JSON Object
data.certificate.ref    | Certificate ID         | String
data.certificate.status | Certificate Status     | String (approved|rejected|pending)
data.certificate.image  | Image URL              | String


### **Error Cases**

An error response will be send if the following conditions are met.

1. An HTML 404 response will be sent if no records exist for provided certificate ID. **[404 Not Found]**
2. Provider is trying to view the certificate which he/she does not own. **[403 Forbidden]**