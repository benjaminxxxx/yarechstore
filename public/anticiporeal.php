<?xml version="1.0" encoding="ISO-8859-1" standalone="no"?><?xml-stylesheet type="text/xsl" href="factura2.1.xsl"?>
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"
    xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
    xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
    xmlns:ccts="urn:oasis:names:specification:ubl:schema:xsd:CoreComponentParameters-2"
    xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
    xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2"
    xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2"
    xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1"
    xmlns:stat="urn:oasis:names:specification:ubl:schema:xsd:DocumentStatusCode-1.0"
    xmlns:udt="urn:un:unece:uncefact:data:draft:UnqualifiedDataTypesSchemaModule:2"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <ext:UBLExtensions>
        <ext:UBLExtension>
            <ext:ExtensionContent>
                <sac:AdditionalInformation>

                </sac:AdditionalInformation>
            </ext:ExtensionContent>
        </ext:UBLExtension>

        <ext:UBLExtension>
            <ext:ExtensionContent>
                <ds:Signature Id="SignSUNAT">
                    <ds:SignedInfo>
                        <ds:CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315" />
                        <ds:SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1" />
                        <ds:Reference URI="">
                            <ds:Transforms>
                                <ds:Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature" />
                            </ds:Transforms>
                            <ds:DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1" />
                            <ds:DigestValue>S7/FZ51Tw3UTcII46XfuwT5yPPg=</ds:DigestValue>
                        </ds:Reference>
                    </ds:SignedInfo>
                    <ds:SignatureValue>xsdQP1Cqcy87XtVcgJQsaoOgHsBaFLWlkDLJ6AaiZ7ibs/y1vvbxqIHlaPmH5HInCucAb5XzqV+Z
                        885dV9tQy/JlZlfgrhYHxSVw5OpQCrub6DgOsBIY9awN7CVSYzi+97pNYVnIDYQciP8Ck/7AF8/g
                        /SCNT2f+hl7K5i5FJenjfwAV/axtK9JCZUTbCof4fi6amFicILi1tpZhBhE7mK/BjPFwysfR0sSW
                        xKnYblatMokjuWrpNWzEjUbz2SBT/4LAQcp9dNzwEGb2I6tKsGXxSEHDKP0OaBZHn7uxIPs63IPW
                        c29rU8WTP+frPj8PfJ93BTQtgAZJ9oLba7khBg==</ds:SignatureValue>
                    <ds:KeyInfo>
                        <ds:X509Data>
                            <ds:X509Certificate>
                                MIIFrjCCBJagAwIBAgIIf9Td7Evg0UswDQYJKoZIhvcNAQELBQAwRjEkMCIGA1UEAwwbTGxhbWEu
                                cGUgU0hBMjU2IFN0YW5kYXJkIENBMREwDwYDVQQKDAhMTEFNQS5QRTELMAkGA1UEBhMCUEUwHhcN
                                MjQwODA5MTYxNTM5WhcNMjcwODA5MTYxNTAwWjCCAVQxKzApBgNVBAkMIkFWLiBHQVJDSUxBU08g
                                REUgTEEgVkVHQSBOUk8uIDE0NzIxIjAgBgkqhkiG9w0BCQEWE3JtZXphZ0BzdW5hdC5nb2IucGUx
                                QTA/BgNVBAMMOFNVUEVSSU5ULiBOQUMuIERFIEFEVUFOQVMgWSBERSBBRE1JTklTVFJBQ0lPTiBU
                                UklCVVRBUklBMRwwGgYDVQQLDBNBR0VOVEUgQVVUT01BVElaQURPMS4wLAYDVQQLDCVWYWxpZGFk
                                byBwb3IgQUJDIElERU5USURBRCBESUdJVEFMIEVSMVQwUgYDVQQKDEtTVVBFUklOVEVOREVOQ0lB
                                IE5BQ0lPTkFMIERFIEFEVUFOQVMgWSBERSBBRE1JTklTVFJBQ0lPTiBUUklCVVRBUklBIC0gU1VO
                                QVQxDTALBgNVBAcMBExJTUExCzAJBgNVBAYTAlBFMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIB
                                CgKCAQEA1apapJOwMj9tBpgKAgjRwIloUk+xwvQZxc0s/4yl4mDSrc/F/PKZ3QBmKb43fFwivqPU
                                vsr68fQTfyLKM14B+xXwlbQ/yh3+87sMyulLhOPmtSOU0PE8Rw0PsmGVxf1L0HPAPc3Gvuhq4Z9D
                                x5ByED6s+VppDmncvctlJ/NrzYZCk9H91OinTTPVZrDtbmqnOYra8PaTRoCI9Fmndeo9Oo7esM7K
                                VX9f53NEd/mNQqzAOrgSXthFuBZN7zOsGxJ4ZGrVzHSlCH/jFLgObEizHrqisgL1cxKj8QvbGLUj
                                NtUuTvR5IY6hwRLCxbliTa84BN10E0jC7hhAnnuEIAFJiwIDAQABo4IBjjCCAYowDAYDVR0TAQH/
                                BAIwADAfBgNVHSMEGDAWgBRdiFut62X7/mii5NlvPVdyou8rmTBnBggrBgEFBQcBAQRbMFkwNQYI
                                KwYBBQUHMAKGKWh0dHA6Ly9jcnQubGxhbWEucGUvbGxhbWFwZXN0YW5kYXJkY2EuY2VyMCAGCCsG
                                AQUFBzABhhRodHRwOi8vb2NzcC5sbGFtYS5wZTAeBgNVHREEFzAVgRNybWV6YWdAc3VuYXQuZ29i
                                LnBlMEYGA1UdIAQ/MD0wOwYNKwYBBAGDl3cAAQADATAqMCgGCCsGAQUFBwIBFhxodHRwczovL2xs
                                YW1hLnBlL3JlcG9zaXRvcnkvMB0GA1UdJQQWMBQGCCsGAQUFBwMCBggrBgEFBQcDBDA6BgNVHR8E
                                MzAxMC+gLaArhilodHRwOi8vY3JsLmxsYW1hLnBlL2xsYW1hcGVzdGFuZGFyZGNhLmNybDAdBgNV
                                HQ4EFgQUuRkPCyM25gAu5se9GraC/UO07X8wDgYDVR0PAQH/BAQDAgbAMA0GCSqGSIb3DQEBCwUA
                                A4IBAQBC8+YM7moZ7bKYCS+VzJ8wzydSboWeNuAs2CfkhI8hRMQr1r5WZg8pDlroCslF/r6Sjbkr
                                juDHcGKJTM2riGEjlOD4CLU9VjOP7Td6CIZyJRLaz63pZYOwE0H0weG/PvYpopDZzJ6AXYzZxwYk
                                J03/x/vwaCss9ZvmsEn2E1xtGbnMe9MEsg8oF0ul3uRn8Qy5gF37fM7YKuF/r4iBS5uSvBcA5J2F
                                bI/PQls7OgICDyb3W16GeluaT9krY5wQxilJSpGVU++nv5NzmRHkquH3ic2It/KP6MZ9X2tfg6Fh
                                pF6JaUz7MSY+J0KnDDcfJlhxgnkgoT5tV3Sr6z5PpQH6</ds:X509Certificate>
                        </ds:X509Data>
                    </ds:KeyInfo>
                </ds:Signature>
            </ext:ExtensionContent>
        </ext:UBLExtension>
    </ext:UBLExtensions>

    <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
    <cbc:CustomizationID>2.0</cbc:CustomizationID>

    <cbc:ID>E001-4</cbc:ID>
    <cbc:IssueDate>2024-09-25</cbc:IssueDate>
    <cbc:IssueTime>21:47:11.0Z</cbc:IssueTime>
    <!-- COD 2106 : TIPO DE OPERACION TAXFREE  -->
    <cbc:InvoiceTypeCode listAgencyName="PE:SUNAT" listID="0104" listName="Tipo de Documento"
        listSchemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo51"
        listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01" name="Tipo de Operacion">01</cbc:InvoiceTypeCode>
    <cbc:Note languageLocaleID="1000">
        <![CDATA[SON:  CINCO Y 90/100  SOLES]]>
    </cbc:Note>
    <cbc:DocumentCurrencyCode listAgencyName="United Nations Economic Commission for Europe" listID="ISO 4217 Alpha"
        listName="Currency">PEN</cbc:DocumentCurrencyCode>

    <cac:Signature>
        <cbc:ID>E001-4</cbc:ID>
        <cac:SignatoryParty>
            <cac:PartyName>
                <cbc:Name>SUNAT</cbc:Name>
            </cac:PartyName>
        </cac:SignatoryParty>
        <cac:DigitalSignatureAttachment>
            <cac:ExternalReference>
                <cbc:URI>SignSUNAT</cbc:URI>
            </cac:ExternalReference>
        </cac:DigitalSignatureAttachment>
    </cac:Signature>
    <cac:AccountingSupplierParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeAgencyName="PE:SUNAT" schemeID="6" schemeName="Documento de Identidad"
                    schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">20611263300</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name />
            </cac:PartyName>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName>
                    <![CDATA[INVERSIONES YARECH S.R.L.]]>
                </cbc:RegistrationName>
                <cac:RegistrationAddress>
                    <cbc:AddressTypeCode listAgencyName="PE:SUNAT" listName="Establecimientos anexos">0
                    </cbc:AddressTypeCode>
                    <cbc:BuildingNumber />
                    <cbc:CitySubdivisionName>-</cbc:CitySubdivisionName>
                    <cbc:CityName>
                        <![CDATA[CARAVELI]]>
                    </cbc:CityName>
                    <cbc:CountrySubentity>
                        <![CDATA[AREQUIPA]]>
                    </cbc:CountrySubentity>
                    <cbc:CountrySubentityCode>
                        <![CDATA[040307]]>
                    </cbc:CountrySubentityCode>
                    <cbc:District>
                        <![CDATA[CHALA]]>
                    </cbc:District>
                    <cac:AddressLine>
                        <cbc:Line>
                            <![CDATA[AV. LAS FLORES A.H. FLORES MZA. 17 LOTE. 4]]>
                        </cbc:Line>
                    </cac:AddressLine>
                    <cac:Country>
                        <cbc:IdentificationCode>PE</cbc:IdentificationCode>
                    </cac:Country>
                </cac:RegistrationAddress>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:AccountingSupplierParty>
    <cac:AccountingCustomerParty>
        <cac:Party>
            <cac:PartyIdentification>
                <!-- COD TIPO DOC. TAXFREE: 7 PASAPORTE, B DOC.IDENTIF.PERS.NAT.NO DOM., G SALVOCONDUCTO -->
                <cbc:ID schemeAgencyName="PE:SUNAT" schemeID="6" schemeName="Documento de Identidad"
                    schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">10776858507</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName>
                    <![CDATA[QUISPE RAMOS BENJAMIN ELEODORO]]>
                </cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:AccountingCustomerParty>
    <!-- INICIO DIRECCION DETALLADA DEL CLIENTE FACTORING -->

    <!--FIN DIRECCION DETALLADA DEL CLIENTE FACTORING -->
    <cac:SellerSupplierParty>
        <cac:Party>

            <cac:PostalAddress>
                <cbc:ID>040307</cbc:ID>
                <cbc:CitySubdivisionName />
                <cbc:CityName>CARAVELI</cbc:CityName>
                <cbc:CountrySubentity>AREQUIPA</cbc:CountrySubentity>
                <cbc:District>CHALA </cbc:District>
                <cac:AddressLine>
                    <cbc:Line>
                        <![CDATA[AV. LAS FLORES - A.H. FLORES MZA. 17   LOTE. 4    AREQUIPA-CARAVELI-CHALA]]>
                    </cbc:Line>
                </cac:AddressLine>
                <cac:Country>
                    <cbc:IdentificationCode>PE</cbc:IdentificationCode>
                </cac:Country>
            </cac:PostalAddress>

        </cac:Party>
    </cac:SellerSupplierParty>

    <!-- Inicio de detracciones -->
    <!-- Fin de detracciones -->

    <!-- INICIO DATOS DE FORMA DE PAGO -->
    <cac:PaymentTerms>
        <cbc:ID>FormaPago</cbc:ID>
        <cbc:PaymentMeansID>Contado</cbc:PaymentMeansID>
    </cac:PaymentTerms>

    <!-- FIN DE DATOS DE FORMA DE PAGO -->
    <!-- INICIO DATOS DE RETENCION -->
    <!-- FIN DE DATOS DE RETENCION -->
    <cac:TaxTotal>
        <cbc:TaxAmount currencyID="PEN">0.90</cbc:TaxAmount>



        <cac:TaxSubtotal>
            <cbc:TaxableAmount currencyID="PEN">5.00</cbc:TaxableAmount>
            <cbc:TaxAmount currencyID="PEN">0.90</cbc:TaxAmount>
            <cac:TaxCategory>
                <cbc:ID schemeAgencyName="United Nations Economic Commission for Europe" schemeID="UN/ECE 5305"
                    schemeName="Tax Category Identifier">S</cbc:ID>
                <cac:TaxScheme>
                    <cbc:ID schemeAgencyName="PE:SUNAT" schemeID="UN/ECE 5153" schemeName="Codigo de tributos">1000
                    </cbc:ID>
                    <cbc:Name>IGV</cbc:Name>
                    <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>
    </cac:TaxTotal>
    <cac:LegalMonetaryTotal>
        <cbc:LineExtensionAmount currencyID="PEN">5.00</cbc:LineExtensionAmount>
        <cbc:AllowanceTotalAmount currencyID="PEN">0.00</cbc:AllowanceTotalAmount>
        <cbc:ChargeTotalAmount currencyID="PEN">0.00</cbc:ChargeTotalAmount>
        <cbc:PrepaidAmount currencyID="PEN">0.00</cbc:PrepaidAmount>

        <cbc:PayableAmount currencyID="PEN">5.90</cbc:PayableAmount>
    </cac:LegalMonetaryTotal>

    <cac:InvoiceLine>
        <cbc:ID>1</cbc:ID>
        <cbc:InvoicedQuantity unitCode="NIU" unitCodeListAgencyName="United Nations Economic Commission for Europe"
            unitCodeListID="UN/ECE rec 20">1.00</cbc:InvoicedQuantity>
        <cbc:LineExtensionAmount currencyID="PEN">5.00</cbc:LineExtensionAmount>

        <cbc:FreeOfChargeIndicator>false</cbc:FreeOfChargeIndicator>

        <cac:PricingReference>
            <cac:AlternativeConditionPrice>
                <cbc:PriceAmount currencyID="PEN">5.90</cbc:PriceAmount>
                <cbc:PriceTypeCode listAgencyName="PE:SUNAT" listName="Tipo de Precio"
                    listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo16">01</cbc:PriceTypeCode>
            </cac:AlternativeConditionPrice>
        </cac:PricingReference>

        <cac:AllowanceCharge>
            <cbc:ChargeIndicator>true</cbc:ChargeIndicator>
            <cbc:Amount currencyID="PEN">0.00</cbc:Amount>
        </cac:AllowanceCharge>

        <!-- TAX Impuestos Item -->
        <cac:TaxTotal>
            <cbc:TaxAmount currencyID="PEN">0.90</cbc:TaxAmount>
            <!-- cac:TaxSubtotal -->


            <!--Excluyentes :: Otro Tributo - Gratuita(Bonificacion) - Exportacion - Onerosa(Bonitificacion) {Gravada, Inafecta, Exonerada} -->
            <cac:TaxSubtotal>
                <cbc:TaxableAmount currencyID="PEN">5.00</cbc:TaxableAmount>
                <cbc:TaxAmount currencyID="PEN">0.90</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cbc:ID schemeAgencyName="United Nations Economic Commission for Europe" schemeID="UN/ECE 5305"
                        schemeName="Tax Category Identifier">S</cbc:ID>
                    <cbc:Percent>18.00</cbc:Percent>
                    <cbc:TaxExemptionReasonCode listAgencyName="PE:SUNAT" listName="Afectacion del IGV"
                        listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo07">10</cbc:TaxExemptionReasonCode>
                    <cac:TaxScheme>
                        <cbc:ID schemeAgencyName="PE:SUNAT" schemeID="UN/ECE 5153" schemeName="Codigo de tributos">1000
                        </cbc:ID>
                        <cbc:Name>IGV</cbc:Name>
                        <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>
            <!-- /cac:TaxSubtotal -->
        </cac:TaxTotal>
        <cac:Item>
            <cbc:Description>
                <![CDATA[PAGO DEL 90% DEL COSTO DE TRAPOS DE LIMPIEZA****Pago Anticipado***]]>
            </cbc:Description>
            <cac:SellersItemIdentification>
                <cbc:ID />
            </cac:SellersItemIdentification>
        </cac:Item>

        <cac:Price>
            <cbc:PriceAmount currencyID="PEN">5.00</cbc:PriceAmount>
        </cac:Price>

    </cac:InvoiceLine>
</Invoice>