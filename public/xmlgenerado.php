<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"
    xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
    xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
    xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
    xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2">
    <ext:UBLExtensions>
        <ext:UBLExtension>
            <ext:ExtensionContent>
                <ds:Signature Id="GreenterSign">
                    <ds:SignedInfo>
                        <ds:CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315" />
                        <ds:SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1" />
                        <ds:Reference URI="">
                            <ds:Transforms>
                                <ds:Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature" />
                            </ds:Transforms>
                            <ds:DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1" />
                            <ds:DigestValue>wL9ww10VjwmOboH2A0vjxHpfi3M=</ds:DigestValue>
                        </ds:Reference>
                    </ds:SignedInfo>
                    <ds:SignatureValue>
                        WfOdjgctS4LSmvD5dOwIIbN7jGPoNFEF8cjQJ3KwugXl126oevfwu1QqYcBup0W4pa6K8+nQZRuQLYdA77KwA/3s5UtA/12Lu0KB7EOKrXWkyNKEFJaLoFGMXrLIpMqDp1Vv6u0OA6sSaNPeTxYYq3zjMZVZk8XsqQPzlRobWxbanmfaMGMReg/sTewHF67umVZgvyk3gUyDG5CfPRfSQ/qg3ICy06qdZCMhW407JCtgJTgfCXJlzN++tAfaNKgjJwLr9WqG+n9X8CFAzeo//qOy0zKe8vBsQMomxcpms49eJf/U1v7RAUvWtqt4sp7AZDOkOTpFUBCujWbIMpAIZQ==
                    </ds:SignatureValue>
                    <ds:KeyInfo>
                        <ds:X509Data>
                            <ds:X509Certificate>
                                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
                            </ds:X509Certificate>
                            <ds:X509Certificate>
                                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
                            </ds:X509Certificate>
                            <ds:X509Certificate>
                                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
                            </ds:X509Certificate>
                            <ds:X509Certificate>
                                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
                            </ds:X509Certificate>
                        </ds:X509Data>
                    </ds:KeyInfo>
                </ds:Signature>
            </ext:ExtensionContent>
        </ext:UBLExtension>
    </ext:UBLExtensions>
    <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
    <cbc:CustomizationID>2.0</cbc:CustomizationID>
    <cbc:ID>F001-41</cbc:ID>
    <cbc:IssueDate>2024-09-25</cbc:IssueDate>
    <cbc:IssueTime>23:29:21</cbc:IssueTime>
    <cbc:InvoiceTypeCode listID="0101" listAgencyName="PE:SUNAT" listName="Tipo de Documento"
        listSchemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo51"
        listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01" name="Tipo de Operacion">01</cbc:InvoiceTypeCode>
    <cbc:Note languageLocaleID="1000">
        <![CDATA[ TRECE CON 90/100 SOLES ]]>
    </cbc:Note>
    <cbc:DocumentCurrencyCode>PEN</cbc:DocumentCurrencyCode>
    <cac:Signature>
        <cbc:ID>SIGN20611263300</cbc:ID>
        <cac:SignatoryParty>
            <cac:PartyIdentification>
                <cbc:ID>20611263300</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name>
                    <![CDATA[ INVERSIONES YARECH S.R.L. ]]>
                </cbc:Name>
            </cac:PartyName>
        </cac:SignatoryParty>
        <cac:DigitalSignatureAttachment>
            <cac:ExternalReference>
                <cbc:URI>#GREENTER-SIGN</cbc:URI>
            </cac:ExternalReference>
        </cac:DigitalSignatureAttachment>
    </cac:Signature>
    <cac:AccountingSupplierParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeID="6">20611263300</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name>
                    <![CDATA[ - ]]>
                </cbc:Name>
            </cac:PartyName>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName>
                    <![CDATA[ INVERSIONES YARECH S.R.L. ]]>
                </cbc:RegistrationName>
                <cac:RegistrationAddress>
                    <cbc:ID>040307</cbc:ID>
                    <cbc:AddressTypeCode>0000</cbc:AddressTypeCode>
                    <cbc:CitySubdivisionName>-</cbc:CitySubdivisionName>
                    <cbc:CityName>CARAVELI</cbc:CityName>
                    <cbc:CountrySubentity>AREQUIPA</cbc:CountrySubentity>
                    <cbc:District>CHALA</cbc:District>
                    <cac:AddressLine>
                        <cbc:Line>
                            <![CDATA[ AV. LAS FLORES MZA. 17 LOTE. 4 A.H. FLORES ]]>
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
                <cbc:ID schemeID="6">20604331600</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName>
                    <![CDATA[ EMPRESA MINERA GOLDEN PALLIS E.I.R.L. - GOLDEN PALLIS E.I.R.L. ]]>
                </cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:AccountingCustomerParty>
    <cac:PaymentTerms>
        <cbc:ID>FormaPago</cbc:ID>
        <cbc:PaymentMeansID>Contado</cbc:PaymentMeansID>
    </cac:PaymentTerms>
    <cac:TaxTotal>
        <cbc:TaxAmount currencyID="PEN">2.13</cbc:TaxAmount>
        <cac:TaxSubtotal>
            <cbc:TaxableAmount currencyID="PEN">11.86</cbc:TaxableAmount>
            <cbc:TaxAmount currencyID="PEN">2.13</cbc:TaxAmount>
            <cac:TaxCategory>
                <cac:TaxScheme>
                    <cbc:ID>1000</cbc:ID>
                    <cbc:Name>IGV</cbc:Name>
                    <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>
        <cac:TaxSubtotal>
            <cbc:TaxableAmount currencyID="PEN">0.00</cbc:TaxableAmount>
            <cbc:TaxAmount currencyID="PEN">0</cbc:TaxAmount>
            <cac:TaxCategory>
                <cac:TaxScheme>
                    <cbc:ID>9998</cbc:ID>
                    <cbc:Name>INA</cbc:Name>
                    <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>
        <cac:TaxSubtotal>
            <cbc:TaxableAmount currencyID="PEN">0.00</cbc:TaxableAmount>
            <cbc:TaxAmount currencyID="PEN">0</cbc:TaxAmount>
            <cac:TaxCategory>
                <cac:TaxScheme>
                    <cbc:ID>9997</cbc:ID>
                    <cbc:Name>EXO</cbc:Name>
                    <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>
        <cac:TaxSubtotal>
            <cbc:TaxableAmount currencyID="PEN">0.00</cbc:TaxableAmount>
            <cbc:TaxAmount currencyID="PEN">0.00</cbc:TaxAmount>
            <cac:TaxCategory>
                <cac:TaxScheme>
                    <cbc:ID>9996</cbc:ID>
                    <cbc:Name>GRA</cbc:Name>
                    <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>
        <cac:TaxSubtotal>
            <cbc:TaxableAmount currencyID="PEN">0.00</cbc:TaxableAmount>
            <cbc:TaxAmount currencyID="PEN">0</cbc:TaxAmount>
            <cac:TaxCategory>
                <cac:TaxScheme>
                    <cbc:ID>9995</cbc:ID>
                    <cbc:Name>EXP</cbc:Name>
                    <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>
    </cac:TaxTotal>
    <cac:LegalMonetaryTotal>
        <cbc:LineExtensionAmount currencyID="PEN">11.86</cbc:LineExtensionAmount>
        <cbc:TaxInclusiveAmount currencyID="PEN">13.99</cbc:TaxInclusiveAmount>
        <cbc:PayableRoundingAmount currencyID="PEN">-0.09</cbc:PayableRoundingAmount>
        <cbc:PayableAmount currencyID="PEN">13.90</cbc:PayableAmount>
    </cac:LegalMonetaryTotal>
    <cac:InvoiceLine>
        <cbc:ID>1</cbc:ID>
        <cbc:InvoicedQuantity unitCode="NIU">1</cbc:InvoicedQuantity>
        <cbc:LineExtensionAmount currencyID="PEN">11.86</cbc:LineExtensionAmount>
        <cac:PricingReference>
            <cac:AlternativeConditionPrice>
                <cbc:PriceAmount currencyID="PEN">14</cbc:PriceAmount>
                <cbc:PriceTypeCode>01</cbc:PriceTypeCode>
            </cac:AlternativeConditionPrice>
        </cac:PricingReference>
        <cac:TaxTotal>
            <cbc:TaxAmount currencyID="PEN">2.13</cbc:TaxAmount>
            <cac:TaxSubtotal>
                <cbc:TaxableAmount currencyID="PEN">11.86</cbc:TaxableAmount>
                <cbc:TaxAmount currencyID="PEN">2.13</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cbc:Percent>18</cbc:Percent>
                    <cbc:TaxExemptionReasonCode>10</cbc:TaxExemptionReasonCode>
                    <cac:TaxScheme>
                        <cbc:ID>1000</cbc:ID>
                        <cbc:Name>IGV</cbc:Name>
                        <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>
        </cac:TaxTotal>
        <cac:Item>
            <cbc:Description>
                <![CDATA[ Afloja todo 450ml ]]>
            </cbc:Description>
            <cac:SellersItemIdentification>
                <cbc:ID>P071</cbc:ID>
            </cac:SellersItemIdentification>
        </cac:Item>
        <cac:Price>
            <cbc:PriceAmount currencyID="PEN">11.86</cbc:PriceAmount>
        </cac:Price>
    </cac:InvoiceLine>
</Invoice>