<?php
/*
 *  Copyright (c) 2026 Borlabs GmbH. All rights reserved.
 *  This file may not be redistributed in whole or significant part.
 *  Content of this file is protected by international copyright laws.
 *
 *  ----------------- Borlabs Cookie IS NOT FREE SOFTWARE -----------------
 *
 *  @copyright Borlabs GmbH, https://borlabs.io
 */

declare(strict_types=1);

namespace Borlabs\Cookie\Dto\Package;

use Borlabs\Cookie\Dto\AbstractDto;

class InstallationPackageDto extends AbstractDto
{
    /**
     * Due to the size of the data and the fact that it is only used by the PackageManager,
     * which acts like a transformer, we did not create DTO's of this structure.
     *
     * Example:
     * <code>
     * {
     *  "components": {
     *      "compatibilityPatches": [
     *          {
     *              "config": {
     *                  "downloadUrl": "string",
     *                  "hash": "string",
     *              },
     *              "key": "string",
     *          }
     *      ],
     *      "contentBlockers": [
     *          "config": {
     *              "javaScriptGlobal": "string",
     *              "javaScriptInitialization": "string",
     *              "locations": [
     *                  {
     *                      "hostname": "string",
     *                      "path": "string",
     *                  }
     *              ],
     *              "previewCss": "string",
     *              "previewHtml": "string",
     *              "previewImage": "string",
     *              "settingsFields": [
     *                  {
     *                      "dataType": bool,
     *                      "defaultValue": "string",
     *                      "isRequired": bool,
     *                      "key": "string",
     *                      "position": int,
     *                      "translations": [
     *                          {
     *                              "alertMessage": "string",
     *                              "description": "string",
     *                              "errorMessage": "string",
     *                              "field": "string",
     *                              "hint": "string",
     *                              "infoMessage": "string",
     *                              "label": "string",
     *                              "language": "string",
     *                              "values": object | null,
     *                              "warningMessage": "string",
     *                          }
     *                      ],
     *                      "validator": "string",
     *                      "validationRegex": "string" | null,
     *                      "values": object | null,
     *                      "visibility": "string",
     *                  }
     *              ],
     *          },
     *          "key": "string",
     *          "name": "string",
     *          "provider": {
     *              "address": "string",
     *              "iabVendorId": int | null,
     *              "key": "string",
     *              "name": "string",
     *              "partners": [
     *                  "string",
     *              ],
     *              "translations": [
     *                  {
     *                      "cookieUrl": "string",
     *                      "description": "string",
     *                      "language": "string",
     *                      "optOutUrl": "string",
     *                      "privacyUrl": "string",
     *                  }
     *              ],
     *          },
     *          "serviceKey": "string",
     *          "translations": [
     *              {
     *                  "description": "string",
     *                  "language": "string",
     *                  "languageStrings": {
     *                      "someKeyA": "someValueA",
     *                      "someKeyB": "someValueB",
     *                  },
     *              }
     *          ],
     *      ],
     *      "scriptBlockers": [
     *          {
     *              "config": {
     *                  "handles": [
     *                      "string",
     *                  ],
     *                  "onExist": {
     *                      "matchingPhrase": "string",
     *                  },
     *                  "phrases": [
     *                      "matchingPhrase",
     *                      "string",
     *                  ],
     *              },
     *              "key": "string",
     *              "name": "string",
     *          }
     *      ],
     *      "services": [
     *          {
     *              "config": [
     *                  {
     *                      "cookies": [
     *                          "location": {
     *                              "hostname": "string",
     *                              "path": "string",
     *                          },
     *                          "name": "string",
     *                          "purpose": "string",
     *                          "translations": [
     *                              {
     *                                  "description": "string",
     *                                  "language": "string",
     *                                  "lifetime": "string",
     *                              },
     *                          ],
     *                          "type": "string",
     *                      ],
     *                      "fallbackCode": "string",
     *                      "locations": [
     *                          {
     *                              "hostname": "string",
     *                              "path": "string",
     *                          }
     *                      ],
     *                      "optInCode": "string",
     *                      "optOutCode": "string",
     *                      "options": [
     *                          {
     *                              "translations": [
     *                                  {
     *                                      "description": "string",
     *                                      "language": "string",
     *                                  }
     *                              ],
     *                              "type": "string",
     *                          }
     *                      ],
     *                      "settingsFields": [
     *                          {
     *                              "See contentBlockers->config->settingsFields",
     *                          }
     *                      ],
     *                  }
     *              ],
     *              "key": "string",
     *              "name": "string",
     *              "provider": {
     *                 "See contentBlockers->provider",
     *              },
     *              "translations": [
     *                  {
     *                      "description": "string",
     *                      "language": "string",
     *                  }
     *              ],
     *          }
     *      ],
     *      "styleBlockers": [
     *          {
     *              "config": {
     *                  "handles": [
     *                      "string",
     *                  ],
     *                  "phrases": [
     *                      "string",
     *                  ],
     *              },
     *              "key": "string",
     *              "name": "string",
     *          }
     *      ],
     *  },
     *  "isFeatured": bool,
     *  "key": "string",
     *  "name": "string",
     *  "requiredBorlabsCookieVersion": "string",
     *  "successorKey": "string",
     *  "thumbnail": "string",
     *  "translations": [
     *      {
     *          "description": "string",
     *          "followUp": "string",
     *          "language": "string",
     *          "preparation": "string" | null,
     *      },
     *  ],
     *  "type": "string",
     *  "updatedAt": "string",
     *  "version": "string"
     * }
     * </code>
     *
     * @see \Borlabs\Cookie\System\Package\PackageManager
     *
     * @var object{
     *  components: array{
     *      compatibilityPatches: array{
     *          config: array{
     *              downloadUrl: string,
     *              hash: string,
     *          },
     *          key: string,
     *      }[],
     *      contentBlockers: array{
     *          config: array{
     *              javaScriptGlobal: string,
     *              javaScriptInitialization: string,
     *              locations: array{
     *                  hostname: string,
     *                  path: string,
     *              }[],
     *              previewCss: string,
     *              previewHtml: string,
     *              previewImage: string,
     *              settingsFields: array{
     *                  dataType: bool,
     *                  defaultValue: string,
     *                  isRequired: bool,
     *                  key: string,
     *                  position: int,
     *                  translations: array{
     *                      alertMessage: string,
     *                      description: string,
     *                      errorMessage: string,
     *                      field: string,
     *                      hint: string,
     *                      infoMessage: string,
     *                      label: string,
     *                      language: string,
     *                      values: object|null,
     *                      warningMessage: string,
     *                  }[],
     *                  validator: string,
     *                  validationRegex: string|null,
     *                  values: object|null,
     *                  visibility: string,
     *              }[],
     *          },
     *          key: string,
     *          name: string,
     *          provider: array{
     *              address: string,
     *              iabVendorId: int|null,
     *              key: string,
     *              name: string,
     *              partners: string[],
     *              translations: array{
     *                  cookieUrl: string,
     *                  description: string,
     *                  language: string,
     *                  optOutUrl: string,
     *                  privacyUrl: string,
     *              }[],
     *          },
     *          serviceKey: string,
     *          translations: array{
     *              description: string,
     *              language: string,
     *              languageStrings: array{
     *                  someKeyA: string,
     *                  someKeyB: string,
     *              },
     *          }[],
     *      }[],
     *      scriptBlockers: array{
     *          config: array{
     *              handles: string[],
     *              onExist: array{
     *                  matchingPhrase: string,
     *              },
     *              phrases: array{
     *                  matchingPhrase: string,
     *                  string,
     *              },
     *          },
     *          key: string,
     *          name: string,
     *      }[],
     *      services: array{
     *          config: array{
     *              cookies: array{
     *                  location: array{
     *                      hostname: string,
     *                      path: string,
     *                  },
     *                  name: string,
     *                  purpose: string,
     *                  translations: array{
     *                      description: string,
     *                      language: string,
     *                      lifetime: string,
     *                  }[],
     *                  type: string,
     *              }[],
     *              fallbackCode: string,
     *              locations: array{
     *                  hostname: string,
     *                  path: string,
     *              }[],
     *              optInCode: string,
     *              optOutCode: string,
     *              options: array{
     *                  translations: array{
     *                      description: string,
     *                      language: string,
     *                  }[],
     *                  type: string,
     *              }[],
     *              settingsFields: array{
     *                  dataType: bool,
     *                  defaultValue: string,
     *                  isRequired: bool,
     *                  key: string,
     *                  position: int,
     *                  translations: array{
     *                      alertMessage: string,
     *                      description: string,
     *                      errorMessage: string,
     *                      field: string,
     *                      hint: string,
     *                      infoMessage: string,
     *                      label: string,
     *                      language: string,
     *                      values: object|null,
     *                      warningMessage: string,
     *                  }[],
     *                  validator: string,
     *                  validationRegex: string|null,
     *                  values: object|null,
     *                  visibility: string,
     *              }[],
     *          }[],
     *          key: string,
     *          name: string,
     *          provider: array{
     *              address: string,
     *              iabVendorId: int|null,
     *              key: string,
     *              name: string,
     *              partners: string[],
     *              translations: array{
     *                  cookieUrl: string,
     *                  description: string,
     *                  language: string,
     *                  optOutUrl: string,
     *                  privacyUrl: string,
     *              }[],
     *          },
     *          translations: array{
     *              description: string,
     *              language: string,
     *          }[],
     *      }[],
     *      styleBlockers: array{
     *          config: array{
     *              handles: string[],
     *              phrases: string[],
     *          },
     *          key: string,
     *          name: string,
     *      }[],
     *  },
     *  isFeatured: bool,
     *  key: string,
     *  name: string,
     *  requiredBorlabsCookieVersion: string,
     *  successorKey: string,
     *  thumbnail: string,
     *  translations: array{
     *      description: string,
     *      followUp: string,
     *      language: string,
     *      preparation: string|null,
     *  }[],
     *  type: string,
     *  updatedAt: string,
     *  version: string
     * }
     */
    public object $data;
}
