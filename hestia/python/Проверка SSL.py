from CheckSSLCert import validate

def check_ssl(domain):
    # Create an from_link object for the domain
    checker = validate.from_link(domain)

    # Check the SSL certificate status
    status = checker.IsActive()
    days_left = checker.getDayLeft()
    info = checker.getInfo()

    # Print the results
    print("SSL Status:", status)
    print("Days Left:", days_left)
    print("Certificate Info:", info)

    # Print the SSL certificate Report
    print(checker)


check_ssl("labinsk.vladles33.ru")
