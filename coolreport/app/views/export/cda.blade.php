<?xml version="1.0" encoding="UTF-8"?>
{{--
 # The ClinicalDocument class is the entry point into the 
 # CDA R-MIM, the root element of a CDA document. 
 #
 # - xmlns: Namespace
 # - xmlns:mif: Model intercange format
 # - xmlns:voc: Vocabulary
 # - xmlns:xsi: XML Schema
 #
 --}}
<ClinicalDocument xmlns="urn:hl7-org:v3" xmlns:mif="urn:hl7-org:v3/mif" xmlns:voc="urn:hl7-org:v3/voc" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:hl7-org:v3 CDA.xsd">
    
    <!-- CDA Header {{--@see http://www.cdapro.com/know/25007 --}} -->
    {{--
     # realmcode
     #
     # The use of “realm” in HL7 standards is focused on 
     # accounting for regional/geographic differences.
     #
     # @see http://www.cdapro.com/know/27195
     --}}
    <realmCode code="US" />


    {{--
     # OID of the HL7 model
     #
     # Health Level 7 (HL7) registered Refined Message Information Models (RMIMs) 
     #
     # Unique identifier for hierachy CDA Release 2
     #
     # @see http://oid-info.com/get/2.16.840.1.113883.1.3
     --}}
    <typeId root="2.16.840.1.113883.1.3" extension="POCD_HD000040" />
    
    
    {{--Templates --}}
    <templateId root="2.16.840.1.113883.10.20.22.1.1" />
    <templateId root="2.16.840.1.113883.10.20.22.1.2" />
    
    
    {{--Unique ID of the document --}}
    <id root="{{ $cda["root"] }}" extension="{{ $document["id"] }}" />


    {{-- 
     # Document type. Logical Observation Identifier Names and Codes (LOINC)
     #
     # @see http://oid-info.com/get/2.16.840.1.113883.6.1
     # @see https://r.details.loinc.org/LOINC/85353-1.html?sections=Comprehensive
     --}}
    <code 
        code="85353-1" 
        codeSystem="2.16.840.1.113883.6.1" 
        codeSystemName="LOINC"
        displayName="Vital signs, weight, height, head circumference, oxygen saturation and BMI panel"
    />


    {{-- Creation time --}}
    <effectiveTime value="{{ $cda["created_at"] }}"/>    


    {{--
     # Confidentiality code
     #
     # Privacy metadata indicating the sender sensitivity classification, 
     # which is based on an analysis of applicable privacy policies and 
     # the risk of harm that could result from unauthorized disclosure. 
     #
     # possible values: Normal, restricted, substance abuse related.
     #
     --}}
    <confidentialityCode code="N" codeSystem="2.16.840.1.113883.5.25" codeSystemName="HL7 ConfidentialityCode" />    


    {{-- languageCode --}}
    <languageCode code="en-US" />
    
    
    {{-- Version @see http://www.cdapro.com/know/25007 --}}
    <setId root="{{ $cda["root"] }}" extension="1" />
    <versionNumber value="1"/>



    {{--
     # recordTarget
     # 
     # Describing the participation that connects the patient 
     # information to the clinical document. 
     #
     # @see http://www.cdapro.com/know/25068
     # @see https://github.com/jddamore/HL7-Task-Force-Examples/blob/master/DEMO_Record_Target_Example.xml
     --}}
    <recordTarget>
        
        {{--
         # Role 
         #
         # Properties of the patient that are related to the context 
         # of their role of “being a patient”
         #
         # @see http://www.cdapro.com/know/25054 
         --}}
        <patientRole>
            
            {{--
             # Id of the patient
             #
             # Identifier of the patient in the healthcare 
             # organization where they are a patient
             #
             # The @root OID below would be specific to an institution's record identifier system
             --}}
            <id root="{{ $cda["root"] }}.{{ $document["id"] }}" extension="{{ $patient["id"] }}" />
            
            
            {{--
             # Patient information
             #
             # Properties of the patient as a person
             #
             # @see http://www.cdapro.com/know/27368
             --}}
            <patient>
            
                {{--name @see http://www.cdapro.com/know/25041 --}}
                <name> {{ $patient["name"] }}</name>
                
                
                {{--Gender @see http://www.cdapro.com/know/27375 --}}
                <administrativeGenderCode code="{{ $patient["genre"] }}" codeSystem="2.16.840.1.113883.5.1" />
                
                
                {{--Birth @see http://www.cdapro.com/know/25058 --}}
                <birthTime value="{{ $patient["birth"] }}" />
                
            </patient>
            {{--Enf of patient information --}}
        
        </patientRole>
        {{--End of patient role --}}

    </recordTarget>
    {{--End of patient --}}    



    {{--Creator of the document --}}
    <author>
    
        {{--Creation --}}
        <time value="{{ $document["date"] }}"/>
        
        
        {{--Author --}}
        <assignedAuthor>
            <id root="{{ $cda['author'] }}" extension="1" />
        </assignedAuthor>
        
    </author>
    {{--End of the creator of the document --}}


    {{-- custodian of the document --}}
    <custodian>
        <assignedCustodian>
            <representedCustodianOrganization>
                <id root="{{ $cda["custodian"] }}"/>
            </representedCustodianOrganization>
        </assignedCustodian>
    </custodian>
    {{-- End of the custodian of the document --}}


    <!-- CDA Body -->
    {{--
     # component
     #
     # @see http://www.cdapro.com/know/24993 What is the high-level CDA document syntax?
     # @see http://www.cdapro.com/know/26762 Clinical act statements
     --}}
    
    <component>    


        {{--
         # The structured body is made up of a series of section 
         # elements. 
         # @see Physical quantities in CDA <http://www.cdapro.com/know/24981>
         # @see Working with the UCUM code system <http://www.cdapro.com/know/24983>
         --}}
        <structuredBody>        

            <component>

                <section>
                
                    <templateId root="2.16.840.1.113883.10.20.22.2.3.1" />


                    <code 
                        code="30954-2" 
                        codeSystem="2.16.840.1.113883.6.1" 
                        codeSystemName="LOINC" 
                        displayName="Relevant diagnostic tests and/or laboratory data" />
                    
                    
                    <!-- Title -->
                    <title>Results of measures</title>



                    <!--  Representation -->
                    <text>
                        @if ($measures["body_weight"])
                        <table>
                            <caption>General body measurements</caption>
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Weight (kg)</th>
                                    <th>Body Mass Index (kg/m2)</th>
                                    <th>Basal metabolic rate (kcal)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($measures["body_weight"] as $measure)
                                    <tr>
                                        <td> {{ $measure['textual_time'] }}</td>
                                        <td> {{ $measure['weight'] }}</td>
                                        <td> {{ $measure['bmi'] }}</td>
                                        <td> {{ $measure['bmr'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif


                        @if ($measures["glucose"])
                        <table>
                            <caption>Glucose measurements</caption>
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Glucose</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($measures["glucose"] as $measure)
                                    <tr>
                                        <td> {{ $measure["textual_time"] }}</td>
                                        <td> {{ $measure["glucose"] }}</td>
                                        <td> {{ $measure["name"] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif


                        @if ($measures["blood_pressure"])
                        <table>
                            <caption>Blood preassure measurements</caption>
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Systoilic (mmHg)</th>
                                    <th>Diastolic (mmHg)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($measures["blood_pressure"] as $measure)
                                    <tr>
                                        <td> {{ $measure["textual_time"] }}</td>
                                        <td> {{ $measure["systolic"] }}</td>
                                        <td> {{ $measure["diastolic"] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif

                        @if ($measures["pulse"])
                        <table>
                            <caption>Pulse measurements</caption>
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>pulse</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($measures["pulse"] as $measure)
                                    <tr>
                                        <td> {{ $measure["textual_time"] }}</td>
                                        <td> {{ $measure["pulse"] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                        
                        
                        @if ($measures["oxygen"])
                        <table>
                            <caption>Oxygen measurements</caption>
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>oxygen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($measures["oxygen"] as $measure)
                                    <tr>
                                        <td> {{ $measure["textual_time"] }}</td>
                                        <td> {{ $measure["oxygen"] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                        
                        
                        @if ($measures["breathing_frequency"])
                        <table>
                            <caption>Breathing frequency measurements</caption>
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>breathing-frequency</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($measures["breathing_frequency"] as $measure)
                                    <tr>
                                        <td> {{ $measure["textual_time"] }}</td>
                                        <td> {{ $measure["breathing_frequency"] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                        
                        
                        @if ($measures["temperature"])
                        <table>
                            <caption>Body temperature measurements</caption>
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>temperature (º C)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($measures["temperature"] as $measure)
                                    <tr>
                                        <td> {{ $measure["textual_time"] }}</td>
                                        <td> {{ $measure["temperature"] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif

                    </text>


                    @if (isset ($measures["body_weight"]))
                        {{--
                         # This observation represents body weight: it allows expression 
                         # in either metric (kg, g) or imperial (lbs, oz.) units. 
                         # 
                         --}}

                        @foreach ($measures["body_weight"] as $measure)
                            @if (isset ($measure["weight"]))
                                <entry>
                                    <observation classCode="OBS" moodCode="EVN">
                                        <code 
                                            codeSystem="{{ $snomed["code"] }}"
                                            codeSystemName="{{ $snomed["name"] }}" 
                                            code="301333006"                                             
                                            displayName="peso-corporal" />
                                        <effectiveTime value="{{ $measure["time"] }}"/>
                                        <value xsi:type="PQ" unit="kg" value="{{ $measure["weight"] }}" />
                                    </observation>
                                </entry>
                            @endif
                        @endforeach



                        {{--
                         # BMI
                         #
                         # This schema represents a person's body mass index (BMI), 
                         # either a single BMI measurement, or the result of aggregating 
                         # several measurements made over time
                         --}}
                        @foreach ($measures["body_weight"] as $measure)
                            @if (isset ($measure["bmi"]))
                                <entry>
                                    <observation classCode="OBS" moodCode="EVN">
                                        <code 
                                            codeSystem="{{ $snomed["code"] }}"
                                            codeSystemName="{{ $snomed["name"] }}" 
                                            code="60621009"                                             
                                            displayName="BMI. Body Mass Index"/>
                                        <effectiveTime value="{{ $measure["time"] }}"/>
                                        <value xsi:type="PQ" unit="kg/m2" value="{{ $measure["bmi"] }}" />
                                    </observation>
                                </entry>
                            @endif
                        @endforeach


                        {{--
                         # BMR
                         #
                         # Basal metabolic rate (observable entity)
                         #
                         # @see http://bioportal.bioontology.org/ontologies/SNOMEDCT?p=classes&conceptid=165109007
                         --}}
                        @foreach ($measures["body_weight"] as $measure)
                            @if (isset ($measure["bmr"]))
                            <entry>
                                <observation classCode="OBS" moodCode="EVN">
                                    <code 
                                        codeSystem="{{ $snomed["code"] }}"
                                        codeSystemName="{{ $snomed["name"] }}" 
                                        code="165109007"                                         
                                        displayName="BMR. Basal Metabolic Rate"/>
                                    <effectiveTime value="{{ $measure["time"] }}"/>
                                    <value xsi:type="PQ" unit="kcal" value="{{ $measure["bmr"] }}" />
                                </observation>
                            </entry>
                            @endif
                        @endforeach                     
                    @endif                    


                    @if (isset ($measures["glucose"]))
                        @foreach ($measures["glucose"] as $measure)
                            @if ($measure["glucose"])
                            <entry>
                                <observation classCode="OBS" moodCode="EVN">
                                    <code 
                                        code="{{ $measure["code"] }}" 
                                        codeSystem="{{ $snomed["code"] }}"
                                        codeSystemName="{{ $snomed["name"] }}" 
                                        displayName="{{ $measure["name"] }}"/>
                                    <effectiveTime value="{{ $measure["time"] }}"/>
                                    <value xsi:type="PQ" unit="mg/dl" value="{{ $measure["glucose"] }}" />
                                </observation>
                            </entry>
                            @endif
                        @endforeach
                    @endif


                    @if (isset ($measures["blood_pressure"]))
                        @foreach ($measures["blood_pressure"] as $measure)
                            @if ($measure["systolic"])
                                <entry>
                                    <observation classCode="OBS" moodCode="EVN">
                                        <code 
                                            codeSystem="{{ $snomed["code"] }}"
                                            codeSystemName="{{ $snomed["name"] }}" 
                                            code="271649006"                                             
                                            displayName="systolic"/>
                                        <effectiveTime value="{{ $measure["time"] }}"/>
                                        <value xsi:type="PQ" unit="mmHg" value="{{ $measure["systolic"] }}" />
                                    </observation>
                                </entry>
                            @endif
                            @if ($measure["diastolic"])
                                <entry>
                                    <observation classCode="OBS" moodCode="EVN">
                                        <code 
                                            codeSystemName="{{ $snomed["name"] }}" 
                                            codeSystem="{{ $snomed["code"] }}"
                                            code="271650006" 
                                            displayName="diastolic"/>
                                        <effectiveTime value="{{ $measure["time"] }}"/>
                                        <value xsi:type="PQ" unit="mmHg" value="{{ $measure["diastolic"] }}" />
                                    </observation>
                                </entry>
                            @endif
                        @endforeach                    
                    @endif


                    @if (isset ($measures["pulse"]))
                        @foreach ($measures["pulse"] as $measure)
                            @if ($measure["pulse"])
                                <entry>
                                    <observation classCode="OBS" moodCode="EVN">
                                        <code 
                                            codeSystem="{{ $snomed["code"] }}"
                                            codeSystemName="{{ $snomed["name"] }}" 
                                            code="366199006" 
                                            displayName="pulse"/>
                                        <effectiveTime value="{{ $measure["time"] }}"/>
                                        <value xsi:type="PQ" value="{{ $measure["pulse"] }}" />
                                    </observation>
                                </entry>
                            @endif
                        @endforeach
                    @endif


                    @if (isset ($measures["oxygen"]))
                        @foreach ($measures["oxygen"] as $measure)
                            @if ($measure["oxygen"])
                                <entry>
                                    <observation classCode="OBS" moodCode="EVN">
                                        <code 
                                            codeSystem="{{ $snomed["code"] }}"
                                            codeSystemName="{{ $snomed["name"] }}" 
                                            code="104847001" 
                                            displayName="oxygen"/>
                                        <effectiveTime value="{{ $measure["time"] }}"/>
                                        <value xsi:type="PQ" value="{{ $measure["oxygen"] }}" />
                                    </observation>
                                </entry>
                            @endif
                        @endforeach
                    @endif



                    @if (isset ($measures["breathing_frequency"]))
                        @foreach ($measures["breathing_frequency"] as $measure)
                            @if ($measure["breathing_frequency"])
                                <entry>
                                    <observation classCode="OBS" moodCode="EVN">
                                        <code 
                                            codeSystem="{{ $snomed["code"] }}"
                                            codeSystemName="{{ $snomed["name"] }}" 
                                            code="86290005" 
                                            displayName="Respiratory rate "/>
                                        <effectiveTime value="{{ $measure["time"] }}"/>
                                        <value xsi:type="PQ" value="{{ $measure["breathing_frequency"] }}" />
                                    </observation>
                                </entry>
                            @endif
                        @endforeach
                    @endif


                    @if (isset ($measures["temperature"]))
                        @foreach ($measures["temperature"] as $measure)
                            @if ($measure["temperature"])
                            <entry>
                                <observation classCode="OBS" moodCode="EVN">
                                    <code 
                                        codeSystem="{{ $snomed["code"] }}"
                                        codeSystemName="{{ $snomed["name"] }}" 
                                        code="386725007" 
                                        displayName="body temperature" />
                                    <effectiveTime value="{{ $measure["time"] }}" />
                                    <value xsi:type="PQ" value="{{ $measure["temperature"] }}" />
                                </observation>
                            </entry>
                            @endif
                        @endforeach
                    @endif
                    

               </section>
            </component>
            <!-- End of measurements -->


            
            
            <!-- Substances administration -->
            @if ($substances)
            <component>
                
                <section>

                    <!-- Title -->
                    <title>Medicine administration</title>


                    <text>
                        <table>
                            <caption>Medication</caption>
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Dosis</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($substances as $substance)
                                    @if ($substance["drug"]["code"])
                                    <tr>
                                        <td>{{ $substance["textual_time"] }}</td>
                                        <td>{{ $substance["drug"]["name"] }}</td>
                                        <td>{{ $substance["drug"]["code"] }}</td>
                                        <td>{{ $substance["doses"] }}</td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>                        

                    </text>
                
                    {{-- substanceAdministration
                     # 
                     # A type of procedure that involves a performer 
                     # introducing or otherwise applying a material into or to the subject
                     # 
                     --}}
                    @foreach ($substances as $substance)
                        @if ($substance["drug"]["code"])
                        <entry>
                            <substanceAdministration classCode="SBADM" moodCode="EVN">
                                
                                {{--Effective time --}}
                                <effectiveTime value="{{ $substance["time"] }}" />
                                
                                
                                {{--Dose Quantity --}}
                                <doseQuantity value="{{ $substance["doses"] }}" />
                                
                                
                                {{--Consumable --}}
                                <consumable>
                                    <manufacturedProduct classCode="MANU">
                                        <manufacturedMaterial>
                                            <code 
                                                codeSystem="{{ $rxnorm["code"] }}"
                                                codeSystemName="{{ $rxnorm["name"] }}"
                                                code="{{ $substance["drug"]["code"] }}"
                                                displayName="{{ $substance["drug"]["name"] }}"
                                            />
                                        </manufacturedMaterial>
                                    </manufacturedProduct>
                                </consumable>
                                
                            </substanceAdministration>
                        </entry>     
                        @endif
                    @endforeach

                </section>

            </component>
            @endif
            <!-- End of substance administration -->

        
        </structuredBody>
        <!-- End of structured body -->
        
    </component>            

</ClinicalDocument>
