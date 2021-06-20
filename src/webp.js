const webp=require('webp-converter');

// this will grant 755 permission to webp executables
webp.grant_permission();

//webp.cwebp("/home/ivan/domains/sweetspeak.local/bundles/images/blog/blog1.png","blog1.webp","-q 80");
const result = webp.cwebp("/home/ivan/domains/sweetspeak.local/bundles/images/blog/dmitry (1).png","blog2.webp","-q 80");
result.then((response) => {
  console.log(response);
});
